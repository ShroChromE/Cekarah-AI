<?php

namespace App\Ai\Support;

class AgentReplyParser
{
    /**
     * Marker that separates the user-facing reply from the metadata JSON.
     */
    public const DELIMITER = '###META###';

    /**
     * Parse a raw agent response into a normalized payload.
     *
     * Supports the current "reply text + ###META### + json" format and falls
     * back to a legacy pure-JSON object (with an embedded "reply" key).
     *
     * @return array{reply: string, intent: string, confidence: float, escalation_suggested: bool, escalation_contacts: array<int, mixed>, sources_used: array<int, mixed>}
     */
    public static function parse(string $raw): array
    {
        $reply = trim($raw);
        $meta = [];

        if (str_contains($raw, self::DELIMITER)) {
            [$replyPart, $metaPart] = explode(self::DELIMITER, $raw, 2);
            $reply = trim($replyPart);
            $meta = self::decodeJson($metaPart);
        } else {
            // Legacy fallback: the whole response may be a JSON object.
            $decoded = self::decodeJson($raw);
            if (isset($decoded['reply'])) {
                $meta = $decoded;
                $reply = (string) $decoded['reply'];
            }
        }

        return [
            'reply' => $reply,
            'intent' => $meta['intent'] ?? 'unclear',
            'confidence' => isset($meta['confidence']) ? (float) $meta['confidence'] : 0.0,
            'escalation_suggested' => (bool) ($meta['escalation_suggested'] ?? false),
            'escalation_contacts' => is_array($meta['escalation_contacts'] ?? null) ? $meta['escalation_contacts'] : [],
            'sources_used' => is_array($meta['sources_used'] ?? null) ? $meta['sources_used'] : [],
        ];
    }

    /**
     * Decode a JSON fragment, tolerating surrounding markdown fences or prose.
     *
     * @return array<string, mixed>
     */
    private static function decodeJson(string $fragment): array
    {
        $text = trim($fragment);

        if (preg_match('/```(?:json)?\s*([\s\S]+?)\s*```/s', $text, $matches)) {
            $text = $matches[1];
        }

        // Isolate the first {...} block if the model added stray prose.
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $text = substr($text, $start, $end - $start + 1);
        }

        $decoded = json_decode(trim($text), true);

        return is_array($decoded) ? $decoded : [];
    }
}
