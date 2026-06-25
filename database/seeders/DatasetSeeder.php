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
 * Seeds the intent-category dataset with REAL, source-traceable data compiled by
 * the team (bencana hidrometeorologi Sumatera Nov 2025 – 2026).
 *
 * Setiap baris dapat ditelusuri ke sumber resmi (BNPB Portal Satu Data, Kemensos
 * Cek Bansos, Komdigi, Kemenko PMK, Kemendagri). Tabel terstruktur diisi di sini;
 * narasi situasi & titik akses bencana diisi sebagai knowledge documents di
 * KnowledgeSeeder agar di-retrieve oleh tool search_disaster_info.
 *
 * Catatan: data ini BUKAN simulasi — Source.is_simulated diset false.
 */
class DatasetSeeder extends Seeder
{
    /** @var array<string, Source> */
    private array $sources = [];

    public function run(): void
    {
        $this->seedSources();
        $events = $this->seedDisasterEvents();
        $this->seedShelterLocations($events);
        $this->seedAidPrograms($events);
        $this->seedClaimVerifications();
        $this->generateEmbeddings();
    }

    private function seedSources(): void
    {
        $rows = [
            'bnpb' => ['name' => 'BNPB — Badan Nasional Penanggulangan Bencana', 'url' => 'https://www.bnpb.go.id/berita/pasca-bencana-di-sumatra-bnpb-percepat-hunian-infrastruktur-vital-dan-operasi-modifikasi-cuaca', 'publisher' => 'Badan Nasional Penanggulangan Bencana', 'source_type' => 'official', 'published_at' => '2026-01-13'],
            'bnpb_posko' => ['name' => 'BNPB — Koordinasi Bantuan via Posko (Sumbar)', 'url' => 'https://www.bnpb.go.id/berita/bantuan-korban-bencana-sumbar-harus-terkoordinasi-melalui-posko', 'publisher' => 'Badan Nasional Penanggulangan Bencana', 'source_type' => 'official', 'published_at' => '2025-12-02'],
            'kemenkopmk' => ['name' => 'Kemenko PMK — Peresmian Huntara Sumbar', 'url' => 'https://www.kemenkopmk.go.id/menko-pmk-resmikan-hunian-sementara-bagi-masyarakat-terdampak-bencana-di-sumatra-barat', 'publisher' => 'Kementerian Koordinator Bidang PMK', 'source_type' => 'official', 'published_at' => '2026-01-24'],
            'kemendagri' => ['name' => 'Kemendagri — Pemulihan Pascabencana Sumatera (via Kompas)', 'url' => 'https://kilaskementerian.kompas.com/kemendagri/read/2026/05/25/19005141/mendagri-pastikan-pemulihan-pascabencana-sumatera-masuk-tahap-pemulihan', 'publisher' => 'Kementerian Dalam Negeri', 'source_type' => 'official', 'published_at' => '2026-05-25'],
            'bnpb_satudata' => ['name' => 'BNPB — Portal Satu Data Bencana Indonesia', 'url' => 'https://data.bnpb.go.id/', 'publisher' => 'Badan Nasional Penanggulangan Bencana', 'source_type' => 'official', 'published_at' => '2025-12-08'],
            'kemensos' => ['name' => 'Kementerian Sosial — Cek Bansos', 'url' => 'https://cekbansos.kemensos.go.id/', 'publisher' => 'Kementerian Sosial RI', 'source_type' => 'official', 'published_at' => '2026-06-25'],
            'kemensos_dtsen' => ['name' => 'Kemensos — Penentuan Sasaran Desil DTSEN', 'url' => 'https://rri.co.id/nasional/2437631/cara-cek-penerima-bansos-bpnt-pkh-triwulan-ii-2026-akses-cekbansoskemensosgoid', 'publisher' => 'Kementerian Sosial RI', 'source_type' => 'official', 'published_at' => '2026-05-24'],
            'kemensos_nominal' => ['name' => 'Kemensos — Nominal BPNT & PKH 2026', 'url' => 'https://www.kompas.tv/info-publik/672308/cara-cek-bansos-pkh-bpnt-juni-2026-pakai-data-ktp-sekalian-cek-status-desil-dtsen', 'publisher' => 'Kementerian Sosial RI', 'source_type' => 'official', 'published_at' => '2026-06-25'],
            'bnpb_dth' => ['name' => 'BNPB — Skema Dana Tunggu Hunian (DTH)', 'url' => 'https://mediaindonesia.com/nusantara/867085/panduan-lengkap-skema-dana-tunggu-hunian-dth-bnpb-dan-syarat-pengajuannya', 'publisher' => 'Badan Nasional Penanggulangan Bencana', 'source_type' => 'official', 'published_at' => '2026-03-05'],
            'satgas_prr' => ['name' => 'Kemendagri / Satgas PRR — Penyaluran Bantuan Bertahap', 'url' => 'https://news.detik.com/berita/d-8479371/bantuan-pascabencana-mengalir-bertahap-pemda-diminta-terus-perbarui-data', 'publisher' => 'Kementerian Dalam Negeri / Satgas PRR', 'source_type' => 'official', 'published_at' => '2026-05-07'],
            'komdigi_pidie' => ['name' => 'Komdigi — Klarifikasi Hoaks Air Laut Naik Pidie Jaya', 'url' => 'https://www.komdigi.go.id/berita/berita-hoaks/detail/hoaks-air-laut-naik-di-wilayah-kabupaten-pidie-jaya', 'publisher' => 'Kementerian Komunikasi dan Digital', 'source_type' => 'fact_check', 'published_at' => '2025-12-01'],
            'komdigi_pantura' => ['name' => 'Komdigi — Klarifikasi Hoaks Air Laut Naik Pantura Jateng', 'url' => 'https://www.komdigi.go.id/berita/berita-hoaks/detail/hoaks-air-laut-naik-bak-tsunami-sapu-pantai-utara-jawa-tengah', 'publisher' => 'Kementerian Komunikasi dan Digital', 'source_type' => 'fact_check', 'published_at' => '2025-12-01'],
            'komdigi_internet' => ['name' => 'Komdigi — Klarifikasi Hoaks Internet Rakyat Gratis', 'url' => 'https://www.komdigi.go.id/berita/berita-komdigi/detail/ini-hoaks-pendaftaran-internet-rakyat-gratis-3-bulan', 'publisher' => 'Kementerian Komunikasi dan Digital', 'source_type' => 'fact_check', 'published_at' => '2026-01-01'],
        ];

        foreach ($rows as $key => $row) {
            $this->sources[$key] = Source::create($row + ['is_simulated' => false]);
        }
    }

    /**
     * Province-level hub events for the 2025–2026 Sumatera flood-landslide
     * disaster. Shelters / aid / claims attach to these.
     *
     * @return array<string, DisasterEvent>
     */
    private function seedDisasterEvents(): array
    {
        $events = [];

        $events['aceh'] = DisasterEvent::create([
            'name' => 'Banjir & Longsor Sumatera 2025–2026 — Provinsi Aceh',
            'type' => 'flood', 'region' => 'Aceh', 'province' => 'Aceh',
            'status' => 'recovery', 'severity' => 'awas', 'started_at' => '2025-11-25',
            'description' => 'Bencana hidrometeorologi (banjir & longsor) di Aceh sejak akhir November 2025. BNPB mencatat korban meninggal terbanyak di Aceh (±550 jiwa) dengan pengungsi terkonsentrasi di Aceh Utara (±67.876 jiwa); status tanggap darurat diperpanjang di enam daerah Aceh. Per Mei 2026 penanganan memasuki fase pemulihan permanen (rehab-rekon), layanan dasar telah normal.',
            'latitude' => 5.5483, 'longitude' => 95.3238,
        ]);

        $events['sumut'] = DisasterEvent::create([
            'name' => 'Banjir & Longsor Sumatera 2025–2026 — Provinsi Sumatera Utara',
            'type' => 'flood', 'region' => 'Sumatera Utara', 'province' => 'Sumatera Utara',
            'status' => 'recovery', 'severity' => 'siaga', 'started_at' => '2025-11-25',
            'description' => 'Banjir dan longsor melanda sejumlah kabupaten/kota Sumatera Utara (a.l. Mandailing Natal, Tapanuli Tengah, Tapanuli Utara) sejak akhir November 2025. BNPB mencatat ±375 korban meninggal di Sumatera Utara. Sejumlah titik terdampak berstatus akses terbatas. Per pertengahan 2026 memasuki tahap pemulihan.',
            'latitude' => 3.5897, 'longitude' => 98.6739,
        ]);

        $events['sumbar'] = DisasterEvent::create([
            'name' => 'Banjir & Longsor Sumatera 2025–2026 — Provinsi Sumatera Barat',
            'type' => 'flood', 'region' => 'Sumatera Barat', 'province' => 'Sumatera Barat',
            'status' => 'recovery', 'severity' => 'siaga', 'started_at' => '2025-11-24',
            'description' => 'Banjir dan longsor di 13 kabupaten/kota Sumatera Barat. BNPB mencatat ±231 korban meninggal. Penyaluran bantuan diwajibkan terkoordinasi melalui posko. Sebanyak 273 unit hunian sementara (huntara) diresmikan di Agam, Padang Pariaman, Lima Puluh Kota, dan Pesisir Selatan; ruas Padang–Bukittinggi via Malalak sempat tidak dapat diakses.',
            'latitude' => -0.9377, 'longitude' => 100.3603,
        ]);

        $this->cite($events['aceh'], ['bnpb', 'kemendagri', 'bnpb_satudata']);
        $this->cite($events['sumut'], ['bnpb', 'bnpb_satudata', 'kemendagri']);
        $this->cite($events['sumbar'], ['bnpb_posko', 'kemenkopmk', 'bnpb_satudata']);

        return $events;
    }

    /**
     * Real posko/instansi points from the BNPB Portal Satu Data spreadsheet.
     * command_post / national_liaison_post are coordination offices (BUKAN tempat
     * pengungsian warga) — ditandai di notes.
     *
     * @param  array<string, DisasterEvent>  $events
     */
    private function seedShelterLocations(array $events): void
    {
        $coordinationNote = 'Pos koordinasi/komando (instansi pemerintah) — untuk koordinasi relawan & penanganan, BUKAN tempat pengungsian umum warga.';
        $evacuationNote = 'Pos pengungsian/lapangan untuk warga terdampak.';

        $shelters = [
            ['event' => 'sumbar', 'name' => 'Kantor Gubernur Sumatera Barat', 'type' => 'command_post', 'address' => 'Kota Padang, Sumatera Barat', 'region' => 'Kota Padang', 'latitude' => -0.937705, 'longitude' => 100.360279, 'notes' => $coordinationNote],
            ['event' => 'sumut', 'name' => 'Badan Penanggulangan Bencana Daerah (BPBD) Provinsi Sumut', 'type' => 'command_post', 'address' => 'Deli Serdang, Sumatera Utara', 'region' => 'Deli Serdang', 'latitude' => 3.599428, 'longitude' => 98.595477, 'notes' => $coordinationNote],
            ['event' => 'sumut', 'name' => 'Kantor Gubernur Sumatera Utara', 'type' => 'command_post', 'address' => 'Kota Medan, Sumatera Utara', 'region' => 'Kota Medan', 'latitude' => 3.580492, 'longitude' => 98.671961, 'notes' => $coordinationNote],
            ['event' => 'aceh', 'name' => 'Kantor Gubernur Aceh', 'type' => 'command_post', 'address' => 'Kota Banda Aceh, Aceh', 'region' => 'Kota Banda Aceh', 'latitude' => 5.570165, 'longitude' => 95.340806, 'notes' => $coordinationNote],
            ['event' => 'sumut', 'name' => 'Kodim Tapanuli Utara', 'type' => 'national_liaison_post', 'address' => 'Tapanuli Utara, Sumatera Utara', 'region' => 'Tapanuli Utara', 'latitude' => 2.021978, 'longitude' => 98.961283, 'notes' => $coordinationNote],
            ['event' => 'sumbar', 'name' => 'Pusdalops BNPB Regional Sumatera', 'type' => 'national_liaison_post', 'address' => 'Kota Padang, Sumatera Barat', 'region' => 'Kota Padang', 'latitude' => -0.953248, 'longitude' => 100.428633, 'notes' => $coordinationNote],
            ['event' => 'aceh', 'name' => 'Koopsud I Lanud Sultan Iskandar Muda', 'type' => 'national_liaison_post', 'address' => 'Aceh Besar, Aceh', 'region' => 'Aceh Besar', 'latitude' => 5.510802, 'longitude' => 95.425853, 'notes' => $coordinationNote],
            ['event' => 'sumbar', 'name' => 'Posko Lapangan Salareh Aia', 'type' => 'field_post', 'address' => 'Salareh Aia, Agam, Sumatera Barat', 'region' => 'Agam', 'latitude' => -0.110778, 'longitude' => 100.059611, 'notes' => $evacuationNote],
            ['event' => 'aceh', 'name' => 'Posko Pengungsian SMU 1 Rantau', 'type' => 'evacuation_post', 'address' => 'Kec. Rantau, Aceh Tamiang, Aceh', 'region' => 'Aceh Tamiang', 'latitude' => 4.332500, 'longitude' => 98.061000, 'notes' => $evacuationNote],
            ['event' => 'sumbar', 'name' => 'Posko Pengungsian Kantor Lurah Pasar Usang', 'type' => 'evacuation_post', 'address' => 'Padang Panjang Barat, Kota Padang Panjang, Sumatera Barat', 'region' => 'Kota Padang Panjang', 'latitude' => 1.745000, 'longitude' => 98.785000, 'notes' => $evacuationNote],
        ];

        foreach ($shelters as $row) {
            $event = $events[$row['event']];
            unset($row['event']);

            $shelter = $event->shelterLocations()->create($row + [
                'capacity' => null, 'occupancy' => null, 'contact' => null, 'is_active' => true,
            ]);
            $this->cite($shelter, ['bnpb_satudata']);
        }
    }

    /**
     * Real national bansos/recovery mechanisms (Kemensos, BNPB, Kemendagri).
     * region = null (berlaku nasional, termasuk untuk korban di Sumatera).
     *
     * @param  array<string, DisasterEvent>  $events
     */
    private function seedAidPrograms(array $events): void
    {
        $programs = [
            [
                'name' => 'Cek Status Penerima PKH/BPNT via Cek Bansos', 'provider' => 'Kementerian Sosial RI',
                'aid_type' => 'pkh', 'region' => 'Nasional',
                'description' => 'Cek status kepesertaan PKH/BPNT/BST/PBI-JKN melalui cekbansos.kemensos.go.id: masukkan NIK 16 digit atau pilih wilayah + nama, isi captcha, klik Cari Data; tersedia juga aplikasi resmi "Cek Bansos". Bila tidak muncul, ajukan usulan via aplikasi/kantor desa/Dinsos. Cekarah tidak meminta/menyimpan NIK — pengguna diarahkan memasukkannya langsung di situs resmi Kemensos.',
                'eligibility' => 'Penerima terdaftar DTSEN; pengecekan via NIK di kanal resmi Kemensos.',
                'schedule_status' => 'ongoing', 'starts_at' => null, 'ends_at' => null, 'source' => 'kemensos',
            ],
            [
                'name' => 'Penentuan Sasaran Bansos Berdasarkan Desil DTSEN', 'provider' => 'Kementerian Sosial RI',
                'aid_type' => 'other', 'region' => 'Nasional',
                'description' => 'Sasaran bansos memakai "desil" DTSEN — mempertimbangkan pekerjaan, pendidikan, kondisi rumah, daya listrik, dan kepemilikan aset (bukan hanya pendapatan). Desil 1–4 prioritas PKH/BPNT, desil 5 berpeluang PBI-JKN. DTSEN diperbarui tiap triwulan; sejak April 2026 dimajukan ke tanggal 10.',
                'eligibility' => 'Masuk desil 1–4 DTSEN untuk PKH/BPNT; desil 5 untuk PBI-JKN.',
                'schedule_status' => 'ongoing', 'starts_at' => null, 'ends_at' => null, 'source' => 'kemensos_dtsen',
            ],
            [
                'name' => 'Nominal Bantuan BPNT & PKH 2026', 'provider' => 'Kementerian Sosial RI',
                'aid_type' => 'bpnt', 'region' => 'Nasional',
                'description' => 'BPNT Rp200.000/bulan (Rp600.000/triwulan) disalurkan via Himbara/PT Pos. PKH bervariasi per kategori: ibu hamil/nifas Rp750.000, anak usia dini 0–6 tahun Rp750.000, anak SD sederajat Rp225.000 per tahap (dapat berubah).',
                'eligibility' => 'Keluarga penerima manfaat terdaftar DTSEN sesuai kategori PKH/BPNT.',
                'schedule_status' => 'ongoing', 'starts_at' => null, 'ends_at' => null, 'source' => 'kemensos_nominal',
            ],
            [
                'name' => 'Dana Tunggu Hunian (DTH) Korban Bencana', 'provider' => 'BNPB',
                'aid_type' => 'dth', 'region' => 'Nasional',
                'description' => 'DTH = bantuan tunai untuk keluarga dengan rumah rusak berat/tidak dapat dihuni akibat bencana, untuk menyewa/tinggal sementara sambil menunggu hunian tetap (Huntap). Umumnya tidak untuk rumah rusak sedang. Cair 1–3 bulan setelah tanggap darurat selesai; sejak 2026 ditransfer nontunai ke rekening.',
                'eligibility' => 'Keluarga dengan rumah rusak berat/tidak dapat dihuni akibat bencana, terverifikasi pemda/BNPB.',
                'schedule_status' => 'ongoing', 'starts_at' => null, 'ends_at' => null, 'source' => 'bnpb_dth',
            ],
            [
                'name' => 'Mekanisme Penyaluran Bantuan Pascabencana Bertahap', 'provider' => 'Kemendagri / Satgas PRR',
                'aid_type' => 'jadup', 'region' => 'Nasional',
                'description' => 'Penyaluran (jaminan hidup/jadup, isi hunian, stimulan ekonomi, perbaikan rumah, DTH) dilakukan bertahap berdasarkan data pemda yang diverifikasi BPS — bukan menunggu pendataan total selesai. Warga yang terlewat dapat diusulkan ulang. Contoh skala: Aceh Utara mengajukan puluhan ribu KK secara bertahap.',
                'eligibility' => 'Korban bencana yang terdata & terverifikasi pemda; data dapat diperbarui berkala.',
                'schedule_status' => 'ongoing', 'starts_at' => null, 'ends_at' => null, 'source' => 'satgas_prr',
            ],
        ];

        foreach ($programs as $row) {
            $source = $row['source'];
            unset($row['source']);

            // National programs are not tied to a single disaster event.
            $program = $events['sumut']->aidPrograms()->create($row + ['is_active' => true]);
            $this->cite($program, [$source]);
        }
    }

    /**
     * Real fact-checks confirmed by Komdigi (Skenario 4).
     */
    private function seedClaimVerifications(): void
    {
        $pidie = ClaimVerification::create([
            'claim_text' => 'Air laut naik / tsunami di wilayah Kabupaten Pidie Jaya, warga diminta segera mengungsi.',
            'status' => 'hoax', 'region' => 'Pidie Jaya, Aceh',
            'explanation' => 'HOAKS. Narasi "air laut naik/tsunami" di Pidie Jaya saat banjir bandang (30 Nov 2025) dikonfirmasi tidak benar oleh Wakil Bupati setelah pengecekan TNI-Polri tidak menemukan anomali. Kabar ini memicu kepanikan massal — ribuan warga berhamburan dan mengganggu operasi SAR; lima orang diamankan sebagai terduga penyebar. Hanya BMKG yang berwenang mengeluarkan peringatan dini tsunami.',
        ]);
        $this->cite($pidie, ['komdigi_pidie']);

        $pantura = ClaimVerification::create([
            'claim_text' => 'Air laut naik bak tsunami menyapu pantai utara Jawa Tengah.',
            'status' => 'hoax', 'region' => 'Pantai Utara Jawa Tengah',
            'explanation' => 'HOAKS. Klaim bermodus "air laut naik seperti tsunami" juga beredar untuk Pantai Utara Jawa Tengah dan telah diklarifikasi tidak benar oleh Komdigi. Pola klaim semacam ini berulang di wilayah pesisir saat cuaca ekstrem; verifikasi selalu ke kanal resmi BMKG.',
        ]);
        $this->cite($pantura, ['komdigi_pantura']);

        $internet = ClaimVerification::create([
            'claim_text' => 'Pendaftaran Internet Rakyat gratis 3 bulan melalui tautan yang beredar.',
            'status' => 'hoax', 'region' => null,
            'explanation' => 'HOAKS / phishing. Unggahan tautan "Internet Rakyat gratis 3 bulan" adalah modus phishing yang meminta nama dan nomor Telegram. Layanan asli hanya melalui internetrakyat.id atau mytelemedia.id. Jangan memasukkan data pribadi pada tautan tidak resmi.',
        ]);
        $this->cite($internet, ['komdigi_internet']);
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
