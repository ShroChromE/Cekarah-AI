<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class GetEscalationContactsTool implements Tool
{
    private const CONTACTS = [
        'navigasi' => [
            ['name' => 'BNPB', 'contact' => '117 ext 7', 'available' => '24 jam'],
            ['name' => 'Basarnas', 'contact' => '115', 'available' => '24 jam'],
            ['name' => 'PMI', 'contact' => '021-7992325', 'available' => '24 jam'],
            ['name' => 'Kemensos', 'contact' => '1500771', 'available' => 'Jam kerja'],
        ],
        'verifikasi' => [
            ['name' => 'BMKG', 'contact' => 'bmkg.go.id / 021-6546318', 'available' => '24 jam'],
            ['name' => 'BNPB', 'contact' => 'bnpb.go.id / 117 ext 7', 'available' => '24 jam'],
            ['name' => 'Kemkomdigi — Aduan Hoaks', 'contact' => 'aduankonten.id', 'available' => '24 jam online'],
            ['name' => 'Aplikasi Info BMKG', 'contact' => 'Unduh di Play Store / App Store', 'available' => '24 jam'],
        ],
        'general' => [
            ['name' => 'BNPB', 'contact' => '117 ext 7', 'available' => '24 jam'],
            ['name' => 'Basarnas', 'contact' => '115', 'available' => '24 jam'],
            ['name' => 'Polisi', 'contact' => '110', 'available' => '24 jam'],
            ['name' => 'Ambulans', 'contact' => '119', 'available' => '24 jam'],
        ],
    ];

    public function description(): string
    {
        return 'Dapatkan daftar kontak petugas resmi yang bisa dihubungi berdasarkan jenis kebutuhan. Panggil jika confidence rendah, situasi darurat, atau user membutuhkan kontak langsung.';
    }

    public function handle(Request $request): string
    {
        $intent = $request['intent'];
        $contacts = self::CONTACTS[$intent] ?? self::CONTACTS['general'];

        return json_encode([
            'intent' => $intent,
            'reason' => $request['reason'],
            'contacts' => $contacts,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'intent' => $schema->string()->enum(['navigasi', 'verifikasi', 'general'])->required(),
            'reason' => $schema->string()->required(),
        ];
    }
}
