<?php

namespace App\Ai\Support;

use App\Models\EmergencyContact;

/**
 * Responsible-AI guards applied around the chat pipeline:
 *  - Privacy: redacts NIK/KK-like 16-digit sequences before the message is sent
 *    to the model or stored, so Cekarah never processes/keeps a citizen's NIK.
 *  - Escalation: flags life-threatening messages so emergency contacts are
 *    surfaced regardless of what the model returns.
 */
class SafetyGuard
{
    /** 16 digits (NIK/KK), tolerating spaces/dots/dashes as separators. */
    private const NIK_PATTERN = '/\b(?:\d[ .\-]?){15}\d\b/';

    /** Strong life-threatening cues (kept conservative to limit false positives). */
    private const DANGER_KEYWORDS = [
        'terjebak', 'terjebak banjir', 'hanyut', 'terseret arus', 'tenggelam',
        'tidak bisa keluar', 'terjebak di atap', 'air terus naik', 'atap roboh',
        'tertimbun', 'tertimpa', 'pendarahan', 'luka parah', 'sekarat', 'pingsan',
        'tersengat listrik', 'minta tolong', 'tolong darurat', 'darurat sekarang',
        'orang hilang', 'anak hilang', 'terjebak di dalam',
    ];

    /**
     * Redact NIK/KK-like sequences from a free-text message.
     *
     * @return array{text: string, redacted: bool}
     */
    public function redactSensitive(string $text): array
    {
        $clean = preg_replace(self::NIK_PATTERN, '[NIK DISENSOR]', $text) ?? $text;

        return ['text' => $clean, 'redacted' => $clean !== $text];
    }

    /**
     * Whether a message describes a life-threatening situation needing escalation.
     */
    public function isLifeThreatening(string $text): bool
    {
        $haystack = ' '.mb_strtolower($text).' ';

        foreach (self::DANGER_KEYWORDS as $keyword) {
            if (str_contains($haystack, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Escalation contacts for emergencies, sourced from the database (with a
     * hardcoded fallback if the table is empty).
     *
     * @return array<int, array{name: string, contact: string, available: string|null}>
     */
    public function escalationContacts(int $limit = 3): array
    {
        $contacts = EmergencyContact::query()
            ->active()
            ->whereIn('category', ['bencana', 'sar', 'kesehatan'])
            ->limit($limit)
            ->get(['name', 'phone', 'availability'])
            ->map(fn (EmergencyContact $c) => [
                'name' => $c->name,
                'contact' => $c->phone,
                'available' => $c->availability,
            ])
            ->all();

        if (! empty($contacts)) {
            return $contacts;
        }

        return [
            ['name' => 'BNPB', 'contact' => '117 ext 7', 'available' => '24 jam'],
            ['name' => 'Basarnas', 'contact' => '115', 'available' => '24 jam'],
        ];
    }
}
