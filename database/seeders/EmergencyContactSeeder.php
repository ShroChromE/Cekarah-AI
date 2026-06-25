<?php

namespace Database\Seeders;

use App\Models\EmergencyContact;
use Illuminate\Database\Seeder;

/**
 * Official Indonesian emergency hotlines. Replaces the previously hardcoded
 * BNPB/Basarnas strings so escalation contacts have a single, auditable source.
 */
class EmergencyContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            ['name' => 'BNPB — Pusat Pengendalian Operasi', 'phone' => '117 ext 7', 'category' => 'bencana', 'availability' => '24 jam', 'description' => 'Pelaporan & bantuan penanggulangan bencana nasional.', 'source_url' => 'https://bnpb.go.id', 'sort_order' => 1],
            ['name' => 'Basarnas — Pencarian & Pertolongan (SAR)', 'phone' => '115', 'category' => 'sar', 'availability' => '24 jam', 'description' => 'Pencarian dan pertolongan korban (orang hilang/terjebak).', 'source_url' => 'https://basarnas.go.id', 'sort_order' => 2],
            ['name' => 'Ambulans / Gawat Darurat Medis', 'phone' => '118 / 119', 'category' => 'kesehatan', 'availability' => '24 jam', 'description' => 'Kegawatdaruratan medis dan evakuasi pasien.', 'source_url' => 'https://www.kemkes.go.id', 'sort_order' => 3],
            ['name' => 'Pemadam Kebakaran', 'phone' => '113', 'category' => 'keamanan', 'availability' => '24 jam', 'description' => 'Kebakaran dan penyelamatan.', 'source_url' => null, 'sort_order' => 4],
            ['name' => 'Polisi', 'phone' => '110', 'category' => 'keamanan', 'availability' => '24 jam', 'description' => 'Keamanan dan ketertiban.', 'source_url' => null, 'sort_order' => 5],
            ['name' => 'PMI — Palang Merah Indonesia', 'phone' => '021-7992325', 'category' => 'sosial', 'availability' => 'Jam kerja', 'description' => 'Bantuan kemanusiaan, donor darah, dapur umum.', 'source_url' => 'https://pmi.or.id', 'sort_order' => 6],
            ['name' => 'Kemensos — Hotline Bantuan Sosial', 'phone' => '1500771', 'category' => 'sosial', 'availability' => 'Jam kerja', 'description' => 'Informasi & pengaduan bantuan sosial (PKH/BPNT/DTSEN).', 'source_url' => 'https://cekbansos.kemensos.go.id', 'sort_order' => 7],
            ['name' => 'BMKG — Informasi Cuaca, Gempa, Tsunami', 'phone' => '021-6546318', 'category' => 'informasi', 'availability' => '24 jam', 'description' => 'Peringatan dini cuaca, gempa, dan tsunami.', 'source_url' => 'https://www.bmkg.go.id', 'sort_order' => 8],
        ];

        foreach ($contacts as $row) {
            EmergencyContact::create($row + ['is_active' => true]);
        }

        $this->command?->info('Seeded '.count($contacts).' emergency contacts.');
    }
}
