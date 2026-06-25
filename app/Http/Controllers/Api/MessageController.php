<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\CekarahAgent;
use App\Ai\Support\AgentReplyParser;
use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Exceptions\FailoverableException;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\StreamedAgentResponse;
use Laravel\Ai\Streaming\Events\TextDelta;
use Laravel\Ai\Streaming\Events\ToolCall;
use Laravel\Ai\Streaming\Events\ToolResult;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MessageController extends Controller
{
    public function store(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $session = ChatSession::where('token', $token)->firstOrFail();

        try {
            $response = $this->runAgent($session, $validated['content']);
            $data = AgentReplyParser::parse($response->text);

            $session->update([
                'last_intent' => $data['intent'],
                'last_confidence_pct' => (int) round($data['confidence'] * 100),
            ]);
            $this->logIntent($session, $validated['content'], $data);

            return response()->json($data, 201);
        } catch (Throwable $e) {
            Log::error('cekarah.message.error', ['token' => $token, 'error' => $e->getMessage()]);

            return response()->json($this->errorPayload($e), 200);
        }
    }

    /**
     * Stream the assistant's reply token-by-token over Server-Sent Events.
     *
     * Event shapes (each as `data: {json}\n\n`):
     *   { type: 'status', message }        — a tool is running
     *   { type: 'chunk', content }         — a slice of the reply text
     *   { type: 'done', reply, intent, ... } — authoritative final payload
     *   { type: 'error', ...payload }      — failure, with emergency contacts
     */
    public function stream(Request $request, string $token): StreamedResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $session = ChatSession::where('token', $token)->firstOrFail();
        $content = $validated['content'];

        return response()->stream(function () use ($session, $content) {
            $delimiter = AgentReplyParser::DELIMITER;
            $buffer = '';
            $emitted = 0;
            $metaReached = false;
            $locations = [];
            $references = [];
            $needsReview = false;

            try {
                $stream = $this->agentForSession($session)->stream($content);

                $stream->then(function (StreamedAgentResponse $response) use ($session) {
                    if (! $session->conversation_id && $response->conversationId) {
                        $session->update(['conversation_id' => $response->conversationId]);
                    }
                });

                foreach ($stream as $event) {
                    if ($event instanceof ToolCall) {
                        $this->sse([
                            'type' => 'status',
                            'message' => $this->toolStatus($event->toolCall->name),
                        ]);

                        continue;
                    }

                    if ($event instanceof ToolResult) {
                        // Capture authoritative structured data straight from the
                        // tool output (coordinates and sources, not via the model).
                        $decoded = is_string($event->toolResult->result)
                            ? json_decode($event->toolResult->result, true)
                            : $event->toolResult->result;

                        if (is_array($decoded)) {
                            $this->collectToolData($event->toolResult->name, $decoded, $locations, $references);

                            if ($this->toolFoundNothing($decoded)) {
                                $needsReview = true;
                            }
                        }

                        continue;
                    }

                    if (! $event instanceof TextDelta) {
                        continue;
                    }

                    $buffer .= $event->delta;

                    if ($metaReached) {
                        continue;
                    }

                    $pos = strpos($buffer, $delimiter);

                    if ($pos === false) {
                        // Hold back the tail in case it is a partial delimiter.
                        $safeEnd = max($emitted, strlen($buffer) - strlen($delimiter) + 1);
                        if ($safeEnd > $emitted) {
                            $this->sse(['type' => 'chunk', 'content' => substr($buffer, $emitted, $safeEnd - $emitted)]);
                            $emitted = $safeEnd;
                        }
                    } else {
                        if ($pos > $emitted) {
                            $this->sse(['type' => 'chunk', 'content' => substr($buffer, $emitted, $pos - $emitted)]);
                        }
                        $emitted = $pos;
                        $metaReached = true;
                    }
                }

                $data = AgentReplyParser::parse($buffer);

                $session->update([
                    'last_intent' => $data['intent'],
                    'last_confidence_pct' => (int) round($data['confidence'] * 100),
                ]);
                $this->logIntent($session, $content, $data, $needsReview);

                $this->sse([
                    'type' => 'done',
                    ...$data,
                    'locations' => $locations,
                    'references' => array_values($references),
                ]);
            } catch (Throwable $e) {
                Log::error('cekarah.stream.error', ['token' => $session->token, 'error' => $e->getMessage()]);
                $this->sse(['type' => 'error', ...$this->errorPayload($e)]);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }

    public function index(string $token): JsonResponse
    {
        $session = ChatSession::where('token', $token)->firstOrFail();

        return response()->json([
            'token' => $session->token,
            'last_intent' => $session->last_intent,
            'last_confidence_pct' => $session->last_confidence_pct,
        ]);
    }

    /**
     * Map a tool name to a friendly Indonesian status label. Matches loosely
     * since the SDK may surface either the snake_case name or the class
     * basename (e.g. "find_shelter_locations" or "FindShelterLocationsTool").
     */
    private function toolStatus(string $name): string
    {
        $key = strtolower($name);

        return match (true) {
            str_contains($key, 'disaster') => 'Mencari informasi bencana…',
            str_contains($key, 'verify') || str_contains($key, 'claim') => 'Memverifikasi klaim ke sumber resmi…',
            str_contains($key, 'shelter') => 'Mencari lokasi posko…',
            str_contains($key, 'aid') => 'Mencari program bantuan…',
            default => 'Memproses…',
        };
    }

    /**
     * Map the model's detected intent to the category tool that handles it.
     */
    private const INTENT_TOOL = [
        'disaster_info' => 'search_disaster_info',
        'claim_verification' => 'verify_claim',
        'shelter_location' => 'find_shelter_locations',
        'aid_assistance' => 'get_aid_assistance_info',
    ];

    /**
     * Extract structured data from a tool result: shelter locations (for the
     * map) and any citations (for clickable source links). Coordinates and
     * URLs come straight from the tool/DB, never from the model's text.
     *
     * @param  array<mixed, mixed>  $decoded
     * @param  array<int, mixed>  $locations
     * @param  array<string, array{name: string, url: string|null, date: string|null}>  $references
     */
    private function collectToolData(string $name, array $decoded, array &$locations, array &$references): void
    {
        if (str_contains(strtolower($name), 'shelter') && ! empty($decoded['locations'])) {
            foreach ($decoded['locations'] as $loc) {
                if (isset($loc['latitude'], $loc['longitude'])) {
                    $locations[] = [
                        'name' => $loc['name'] ?? 'Lokasi',
                        'type' => $loc['type'] ?? null,
                        'address' => $loc['address'] ?? null,
                        'latitude' => (float) $loc['latitude'],
                        'longitude' => (float) $loc['longitude'],
                        'capacity' => $loc['capacity'] ?? null,
                        'occupancy' => $loc['occupancy'] ?? null,
                        'contact' => $loc['contact'] ?? null,
                        'notes' => $loc['notes'] ?? null,
                    ];
                }
            }
        }

        $this->collectReferences($decoded, $references);
    }

    /**
     * Determine whether a tool returned a total fallback (no DB data and no web
     * research) — used to flag the message for the volunteer review queue.
     *
     * @param  array<mixed, mixed>  $decoded
     */
    private function toolFoundNothing(array $decoded): bool
    {
        $emptyResult = (($decoded['found'] ?? null) === false)
            || (($decoded['status'] ?? null) === 'no_official_data');

        return $emptyResult && empty($decoded['web_research']);
    }

    /**
     * Recursively gather every "references" entry in a tool result, de-duped.
     *
     * @param  array<mixed, mixed>  $node
     * @param  array<string, array{name: string, url: string|null, date: string|null}>  $references
     */
    private function collectReferences(array $node, array &$references): void
    {
        foreach ($node as $key => $value) {
            if ($key === 'references' && is_array($value)) {
                foreach ($value as $ref) {
                    if (is_array($ref) && ! empty($ref['name'])) {
                        $signature = $ref['name'].'|'.($ref['url'] ?? '');
                        $references[$signature] = [
                            'name' => $ref['name'],
                            'url' => $ref['url'] ?? null,
                            'date' => $ref['date'] ?? null,
                        ];
                    }
                }
            } elseif (is_array($value)) {
                $this->collectReferences($value, $references);
            }
        }
    }

    /**
     * Persist the detected intent for analytics. The intent comes from the
     * model's own classification in the response metadata; out-of-scope and
     * error replies are logged with a null tool.
     *
     * @param  array<string, mixed>  $data
     */
    private function logIntent(ChatSession $session, string $userMessage, array $data, bool $needsReview = false): void
    {
        $intent = $data['intent'] ?? 'out_of_scope';

        $session->intentLogs()->create([
            'conversation_id' => $session->conversation_id,
            'user_message' => $userMessage,
            'detected_intent' => $intent,
            'tool_called' => self::INTENT_TOOL[$intent] ?? null,
            'needs_review' => $needsReview,
            'confidence' => $data['confidence'] ?? null,
        ]);
    }

    /**
     * Write a single SSE frame and flush it to the client immediately.
     *
     * @param  array<string, mixed>  $data
     */
    private function sse(array $data): void
    {
        echo 'data: '.json_encode($data, JSON_UNESCAPED_UNICODE)."\n\n";

        if (ob_get_level() > 0) {
            @ob_flush();
        }

        flush();
    }

    /**
     * Run the agent (non-streaming), retrying once on a transient overload.
     */
    private function runAgent(ChatSession $session, string $content): AgentResponse
    {
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $agent = $this->agentForSession($session);
                $response = $agent->prompt($content);

                if (! $session->conversation_id && $response->conversationId) {
                    $session->update(['conversation_id' => $response->conversationId]);
                }

                return $response;
            } catch (Throwable $e) {
                if (! $e instanceof FailoverableException || $attempt >= 2) {
                    throw $e;
                }

                usleep(1_500_000);
            }
        }

        throw new RuntimeException('AI agent failed without a specific error.');
    }

    /**
     * Build a configured agent, continuing the conversation when it still exists.
     */
    private function agentForSession(ChatSession $session): CekarahAgent
    {
        $conversationExists = $session->conversation_id
            && DB::table('agent_conversations')->where('id', $session->conversation_id)->exists();

        return $conversationExists
            ? (new CekarahAgent)->continue($session->conversation_id, $session)
            : (new CekarahAgent)->forUser($session);
    }

    /**
     * Build a user-facing error payload tailored to the failure type.
     *
     * @return array<string, mixed>
     */
    private function errorPayload(Throwable $e): array
    {
        $message = match (true) {
            $e instanceof FailoverableException => 'Sistem AI sedang sibuk. Tunggu beberapa detik lalu coba lagi. '
                ."Untuk situasi darurat jangan menunggu — hubungi:\n• BNPB: 117 ext 7 (24 jam)\n• Basarnas: 115",
            str_contains($e->getMessage(), 'timed out') => 'Permintaan memakan waktu terlalu lama. Coba sederhanakan pertanyaan, atau '
                ."untuk darurat hubungi:\n• BNPB: 117 ext 7 (24 jam)\n• Basarnas: 115",
            default => "Koneksi ke sistem AI terputus. Untuk situasi darurat hubungi langsung:\n• BNPB: 117 ext 7 (24 jam)\n• Basarnas: 115",
        };

        return [
            'reply' => $message,
            'intent' => 'error',
            'confidence' => 0,
            'escalation_suggested' => true,
            'escalation_contacts' => [
                ['name' => 'BNPB', 'contact' => '117 ext 7', 'available' => '24 jam'],
                ['name' => 'Basarnas', 'contact' => '115', 'available' => '24 jam'],
            ],
            'sources_used' => [],
        ];
    }
}
