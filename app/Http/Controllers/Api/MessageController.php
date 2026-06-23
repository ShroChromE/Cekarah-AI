<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\CekarahAgent;
use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Exceptions\FailoverableException;
use Laravel\Ai\Responses\AgentResponse;
use RuntimeException;
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

            $data = $this->parseAgentResponse($response->text);

            $session->update([
                'last_intent' => $data['intent'] ?? null,
                'last_confidence_pct' => isset($data['confidence'])
                    ? (int) round($data['confidence'] * 100)
                    : null,
            ]);

            return response()->json([
                'reply' => $data['reply'] ?? '',
                'intent' => $data['intent'] ?? 'unclear',
                'confidence' => $data['confidence'] ?? 0,
                'escalation_suggested' => $data['escalation_suggested'] ?? false,
                'escalation_contacts' => $data['escalation_contacts'] ?? [],
                'sources_used' => $data['sources_used'] ?? [],
            ], 201);

        } catch (Throwable $e) {
            Log::error('cekarah.message.error', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);

            return response()->json($this->errorPayload($e), 200);
        }
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
     * Run the agent, retrying once on a transient provider overload.
     */
    private function runAgent(ChatSession $session, string $content): AgentResponse
    {
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                return $this->promptAgent($session, $content);
            } catch (FailoverableException $e) {
                // 503 overloaded / rate limited — retry once after a short backoff.
                if ($attempt >= 2) {
                    throw $e;
                }

                usleep(1_500_000);
            }
        }

        throw new RuntimeException('AI agent failed without a specific error.');
    }

    /**
     * Invoke the agent, continuing the conversation only when it still exists.
     */
    private function promptAgent(ChatSession $session, string $content): AgentResponse
    {
        $conversationExists = $session->conversation_id
            && DB::table('agent_conversations')->where('id', $session->conversation_id)->exists();

        if ($conversationExists) {
            return (new CekarahAgent)->continue($session->conversation_id, $session)->prompt($content);
        }

        // No conversation yet, or the stored one was pruned — start fresh.
        $response = (new CekarahAgent)->forUser($session)->prompt($content);
        $session->update(['conversation_id' => $response->conversationId]);

        return $response;
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

    /**
     * @return array<string, mixed>
     */
    private function parseAgentResponse(string $text): array
    {
        // Strip markdown code fences if the model wrapped the JSON
        if (preg_match('/```(?:json)?\s*([\s\S]+?)\s*```/s', $text, $matches)) {
            $text = $matches[1];
        }

        return json_decode(trim($text), true) ?? ['reply' => $text];
    }
}
