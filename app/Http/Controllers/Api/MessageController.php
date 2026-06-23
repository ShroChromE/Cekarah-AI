<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\CekarahAgent;
use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MessageController extends Controller
{
    public function store(Request $request, string $token): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $session = ChatSession::where('token', $token)->firstOrFail();

        try {
            $agent = new CekarahAgent;

            if ($session->conversation_id) {
                $response = $agent->continue($session->conversation_id, $session)
                    ->prompt($request->content);
            } else {
                $response = $agent->forUser($session)->prompt($request->content);
                $session->update(['conversation_id' => $response->conversationId]);
            }

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

            return response()->json([
                'reply' => "Koneksi ke sistem AI terputus. Untuk situasi darurat hubungi langsung:\n• BNPB: 117 ext 7 (24 jam)\n• Basarnas: 115",
                'intent' => 'error',
                'confidence' => 0,
                'escalation_suggested' => true,
                'escalation_contacts' => [
                    ['name' => 'BNPB', 'contact' => '117 ext 7', 'available' => '24 jam'],
                    ['name' => 'Basarnas', 'contact' => '115', 'available' => '24 jam'],
                ],
                'sources_used' => [],
            ], 200);
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

    private function parseAgentResponse(string $text): array
    {
        // Strip markdown code fences if the model wrapped the JSON
        if (preg_match('/```(?:json)?\s*([\s\S]+?)\s*```/s', $text, $matches)) {
            $text = $matches[1];
        }

        return json_decode(trim($text), true) ?? ['reply' => $text];
    }
}
