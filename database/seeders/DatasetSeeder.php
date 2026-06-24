<?php

namespace Database\Seeders;

use App\Models\ClaimVerification;
use App\Models\DisasterEvent;
use App\Models\Source;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Embeddings;

/**
 * Seeds the intent-category dataset with synthetic-but-sourced data.
 *
 * All records are illustrative (is_simulated = true) yet modeled on the real
 * 2025 Sumatera hydrometeorological disaster and reference genuine official
 * institutions (BNPB, BMKG, BPBD, Kemensos, PMI, MAFINDO, Kemkomdigi).
 */
class DatasetSeeder extends Seeder
{
    /** @var array<string, Source> */
    private array $sources = [];

    public function run(): void
    {
        $this->seedSources();
        $events = $this->seedDisasterEvents();
        $this->seedShelterLocations($events['binjai']);
        $this->seedAidPrograms($events['binjai']);
        $this->seedClaimVerifications($events);
        $this->generateEmbeddings();
    }

    private function seedSources(): void
    {
        $rows = [
            'bnpb' => ['name' => 'BNPB — Pusat Pengendalian Operasi', 'url' => 'https://bnpb.go.id', 'publisher' => 'Badan Nasional Penanggulangan Bencana', 'source_type' => 'official', 'published_at' => '2025-11-27'],
            'bmkg' => ['name' => 'BMKG — Peringatan Dini Cuaca', 'url' => 'https://www.bmkg.go.id', 'publisher' => 'Badan Meteorologi, Klimatologi, dan Geofisika', 'source_type' => 'official', 'published_at' => '2025-11-26'],
            'bpbd_sumut' => ['name' => 'BPBD Provinsi Sumatera Utara', 'url' => 'https://bpbd.sumutprov.go.id', 'publisher' => 'Pemprov Sumatera Utara', 'source_type' => 'official', 'published_at' => '2025-11-27'],
            'bpbd_binjai' => ['name' => 'BPBD Kota Binjai', 'url' => 'https://binjaikota.go.id', 'publisher' => 'Pemerintah Kota Binjai', 'source_type' => 'official', 'published_at' => '2025-11-27'],
            'kemensos' => ['name' => 'Kemensos — Cek Bansos', 'url' => 'https://cekbansos.kemensos.go.id', 'publisher' => 'Kementerian Sosial RI', 'source_type' => 'official', 'published_at' => '2025-11-28'],
            'pmi' => ['name' => 'Palang Merah Indonesia', 'url' => 'https://pmi.or.id', 'publisher' => 'PMI Pusat', 'source_type' => 'official', 'published_at' => '2025-11-27'],
            'mafindo' => ['name' => 'MAFINDO — TurnBackHoax', 'url' => 'https://turnbackhoax.id', 'publisher' => 'Masyarakat Anti Fitnah Indonesia', 'source_type' => 'fact_check', 'published_at' => '2025-12-10'],
            'kemkomdigi' => ['name' => 'Kemkomdigi — Aduan Konten', 'url' => 'https://aduankonten.id', 'publisher' => 'Kementerian Komunikasi dan Digital', 'source_type' => 'official', 'published_at' => '2025-12-09'],
        ];

        foreach ($rows as $key => $row) {
            $this->sources[$key] = Source::create($row + ['is_simulated' => true]);
        }
    }

    /**
     * @return array<string, DisasterEvent>
     */
    private function seedDisasterEvents(): array
    {
        $events = [];

        $events['sumut'] = DisasterEvent::create([
            'name' => 'Banjir Hidrometeorologi Sumatera Utara November 2025',
            'type' => 'flood', 'region' => 'Sumatera Utara', 'province' => 'Sumatera Utara',
            'status' => 'active', 'severity' => 'awas', 'started_at' => '2025-11-25',
            'description' => 'Banjir meluas akibat curah hujan ekstrem di sejumlah kabupaten/kota Sumatera Utara sejak 25 November 2025. Ribuan warga mengungsi dan sejumlah akses jalan terputus. Status tanggap darurat ditetapkan di beberapa wilayah.',
            'latitude' => 3.5952, 'longitude' => 98.6722,
        ]);

        $events['binjai'] = DisasterEvent::create([
            'name' => 'Banjir Kota Binjai November 2025',
            'type' => 'flood', 'region' => 'Binjai', 'province' => 'Sumatera Utara',
            'status' => 'active', 'severity' => 'siaga', 'started_at' => '2025-11-26',
            'description' => 'Luapan Sungai Mencirim dan Sungai Bingai merendam beberapa kelurahan di Kota Binjai dengan ketinggian air 50–150 cm. Sejumlah keluarga dievakuasi ke posko pengungsian yang disiapkan BPBD Kota Binjai.',
            'latitude' => 3.6001, 'longitude' => 98.4854,
        ]);

        $events['langkat'] = DisasterEvent::create([
            'name' => 'Banjir Kabupaten Langkat November 2025',
            'type' => 'flood', 'region' => 'Langkat', 'province' => 'Sumatera Utara',
            'status' => 'active', 'severity' => 'siaga', 'started_at' => '2025-11-26',
            'description' => 'Banjir merendam kawasan permukiman dan persawahan di Kabupaten Langkat akibat hujan deras berkepanjangan dan luapan sungai. Sebagian warga mengungsi ke fasilitas umum.',
            'latitude' => 3.7667, 'longitude' => 98.4500,
        ]);

        $events['tapsel'] = DisasterEvent::create([
            'name' => 'Banjir dan Longsor Tapanuli Selatan November 2025',
            'type' => 'flood', 'region' => 'Tapanuli Selatan', 'province' => 'Sumatera Utara',
            'status' => 'recovery', 'severity' => 'waspada', 'started_at' => '2025-11-24',
            'description' => 'Banjir disertai tanah longsor di beberapa titik Tapanuli Selatan. Fase tanggap darurat berangsur beralih ke pemulihan dengan pembersihan material dan perbaikan akses.',
            'latitude' => 1.3776, 'longitude' => 99.2719,
        ]);

        $this->cite($events['sumut'], ['bnpb', 'bmkg', 'bpbd_sumut']);
        $this->cite($events['binjai'], ['bpbd_binjai', 'bnpb']);
        $this->cite($events['langkat'], ['bpbd_sumut', 'bnpb']);
        $this->cite($events['tapsel'], ['bpbd_sumut', 'bmkg']);

        return $events;
    }

    private function seedShelterLocations(DisasterEvent $binjai): void
    {
        $shelters = [
            [
                'name' => 'Posko Pengungsian GOR Binjai', 'type' => 'evacuation_shelter',
                'address' => 'Jl. Jenderal Gatot Subroto, Kel. Timbang Langkat, Binjai Timur, Kota Binjai',
                'region' => 'Binjai', 'latitude' => 3.6092, 'longitude' => 98.4943,
                'capacity' => 500, 'occupancy' => 320, 'contact' => 'Posko BPBD Binjai (0852-xxxx-xxxx)',
                'notes' => 'Menerima pengungsi umum; tersedia dapur umum dan layanan kesehatan dasar.',
            ],
            [
                'name' => 'Dapur Umum Kantor Kelurahan Pahlawan', 'type' => 'public_kitchen',
                'address' => 'Jl. Perintis Kemerdekaan, Kel. Pahlawan, Binjai Utara, Kota Binjai',
                'region' => 'Binjai', 'latitude' => 3.6171, 'longitude' => 98.4866,
                'capacity' => 800, 'occupancy' => null, 'contact' => 'PMI Kota Binjai',
                'notes' => 'Distribusi makanan siap saji 3x sehari untuk warga terdampak sekitar.',
            ],
            [
                'name' => 'Pos Kesehatan Puskesmas Binjai Kota', 'type' => 'health_post',
                'address' => 'Jl. Soekarno-Hatta No. 12, Kel. Satria, Binjai Kota, Kota Binjai',
                'region' => 'Binjai', 'latitude' => 3.5953, 'longitude' => 98.4851,
                'capacity' => 120, 'occupancy' => null, 'contact' => 'Dinkes Kota Binjai',
                'notes' => 'Layanan pengobatan darurat, ibu hamil, lansia, dan rujukan ke RSUD.',
            ],
        ];

        foreach ($shelters as $row) {
            $shelter = $binjai->shelterLocations()->create($row);
            $this->cite($shelter, ['bpbd_binjai', 'pmi']);
        }
    }

    private function seedAidPrograms(DisasterEvent $binjai): void
    {
        $programs = [
            [
                'name' => 'Bantuan Logistik Darurat Korban Banjir', 'provider' => 'BNPB',
                'aid_type' => 'logistics', 'region' => 'Binjai',
                'description' => 'Paket logistik darurat (sembako, selimut, perlengkapan bayi, terpal) untuk keluarga terdampak banjir yang berada di pengungsian.',
                'eligibility' => 'Keluarga terdampak yang terdata di posko/BPBD setempat.',
                'schedule_status' => 'ongoing', 'starts_at' => '2025-11-27', 'ends_at' => null,
            ],
            [
                'name' => 'Bantuan Sosial Tunai (BST) Korban Bencana', 'provider' => 'Kementerian Sosial',
                'aid_type' => 'cash', 'region' => 'Binjai',
                'description' => 'Bantuan tunai bagi keluarga terdampak bencana sebagai bagian dari skema perlindungan sosial pascabencana.',
                'eligibility' => 'WNI ber-KTP, terdata DTSEN (desil 1–4), bukan ASN/TNI/Polri aktif; verifikasi via Cek Bansos.',
                'schedule_status' => 'planned', 'starts_at' => '2025-12-05', 'ends_at' => null,
            ],
        ];

        foreach ($programs as $row) {
            $program = $binjai->aidPrograms()->create($row);
            $this->cite($program, $row['provider'] === 'BNPB' ? ['bnpb'] : ['kemensos']);
        }
    }

    /**
     * @param  array<string, DisasterEvent>  $events
     */
    private function seedClaimVerifications(array $events): void
    {
        $claim = $events['binjai']->claimVerifications()->create([
            'claim_text' => 'Akan terjadi banjir besar/bandang di Binjai hari ini, sebarkan agar warga segera mengungsi.',
            'status' => 'no_official_data', 'region' => 'Binjai',
            'explanation' => 'Hingga kini tidak ada peringatan dini resmi dari BMKG atau BPBD yang menyatakan akan terjadi banjir besar di Binjai pada hari tertentu. Peringatan dini cuaca/banjir hanya sah jika dikeluarkan BMKG/BPBD melalui kanal resmi. Pesan berantai tanpa rujukan sumber resmi patut diwaspadai sebagai misinformasi yang dapat memicu kepanikan.',
        ]);
        $this->cite($claim, ['bmkg', 'bpbd_binjai']);

        $hoax = ClaimVerification::create([
            'claim_text' => 'Air laut naik dan akan terjadi tsunami, warga pesisir diminta mengungsi (kasus Pidie Jaya, Aceh).',
            'status' => 'hoax', 'region' => 'Aceh',
            'explanation' => 'Kabar "air laut naik" yang memicu evakuasi panik di Pidie Jaya, Aceh, merupakan hoaks yang telah diklarifikasi. BMKG menegaskan tidak ada potensi tsunami; fenomena yang terjadi adalah pasang air laut (rob) biasa. Hanya BMKG yang berwenang mengeluarkan peringatan dini tsunami, umumnya dalam hitungan menit setelah gempa besar dengan parameter tertentu.',
        ]);
        $this->cite($hoax, ['bmkg', 'mafindo', 'kemkomdigi']);

        $quake = ClaimVerification::create([
            'claim_text' => 'Akan ada gempa susulan berkekuatan sangat besar malam ini, info dari orang dalam BMKG.',
            'status' => 'hoax', 'region' => 'Sumatera Utara',
            'explanation' => 'Klaim "gempa susulan dahsyat" dengan waktu spesifik adalah hoaks. Secara ilmiah, waktu, lokasi, dan magnitudo gempa (termasuk gempa susulan) TIDAK dapat diprediksi secara pasti. BMKG tidak pernah merilis prediksi gempa dengan tanggal/jam pasti. Abaikan pesan berantai semacam ini dan rujuk hanya ke kanal resmi BMKG.',
        ]);
        $this->cite($quake, ['bmkg', 'mafindo']);
    }

    /**
     * Attach one or more sources (by key) to a citable record.
     *
     * @param  array<int, string>  $keys
     */
    private function cite(Model $record, array $keys): void
    {
        foreach ($keys as $key) {
            $record->sources()->attach($this->sources[$key]->id);
        }
    }

    /**
     * Generate embeddings for the semantically-searched tables so their vector
     * columns are not left empty (disaster_events & claim_verifications).
     */
    private function generateEmbeddings(): void
    {
        $this->embedRecords(
            DisasterEvent::all(),
            fn (DisasterEvent $e) => "{$e->name}. {$e->description} Wilayah: {$e->region}.",
        );

        $this->embedRecords(
            ClaimVerification::all(),
            fn (ClaimVerification $c) => "{$c->claim_text} {$c->explanation}",
        );
    }

    /**
     * @param  Collection<int, Model>  $records
     * @param  callable(Model): string  $toText
     */
    private function embedRecords($records, callable $toText): void
    {
        if ($records->isEmpty()) {
            return;
        }

        $texts = $records->map($toText)->all();

        try {
            $response = Embeddings::for($texts)->generate();

            foreach ($records as $i => $record) {
                $record->update(['embedding' => $response->embeddings[$i]]);
            }
        } catch (\Throwable $e) {
            Log::warning('DatasetSeeder embedding generation failed', ['error' => $e->getMessage()]);
            $this->command?->warn('  Embedding generation failed: '.$e->getMessage());
        }
    }
}
