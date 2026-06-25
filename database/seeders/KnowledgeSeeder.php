<?php

namespace Database\Seeders;

use App\Models\KnowledgeDocument;
use Illuminate\Database\Seeder;

/**
 * Knowledge base for the disaster-info RAG retrieval.
 *
 * Only REAL, source-traceable documents (BNPB, Kemenko PMK, Kemendagri, BNPB
 * Portal Satu Data) — the earlier synthetic "how-to" documents were removed so
 * the knowledge base contains no fabricated content. Indexed by `knowledge:index`.
 */
class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [

            [
                'title' => 'Progres Penanganan Darurat Banjir-Longsor Sumatera per 12 Januari 2026',
                'category' => 'bencana',
                'topic' => 'update-situasi',
                'source_name' => 'BNPB - bnpb.go.id',
                'source_url' => 'https://www.bnpb.go.id/berita/pasca-bencana-di-sumatra-bnpb-percepat-hunian-infrastruktur-vital-dan-operasi-modifikasi-cuaca',
                'source_date' => '2026-01-13',
                'content' => <<<'TEXT'
BNPB melaporkan total korban meninggal dunia akibat banjir-longsor Sumatera mencapai 1.189 jiwa (Aceh 550, Sumatera Utara 375, Sumatera Barat 231, dan 33 jiwa dalam proses identifikasi). Korban hilang tercatat 141 orang dan pengungsi 195.542 jiwa, dengan jumlah terbanyak di Kabupaten Aceh Utara (67.876 jiwa). Status tanggap darurat diperpanjang di enam daerah di Aceh. BNPB mempercepat penyediaan hunian, perbaikan infrastruktur vital, dan operasi modifikasi cuaca.
TEXT,
            ],

            [
                'title' => 'Koordinasi Penyaluran Bantuan Logistik Melalui Posko — Sumatera Barat',
                'category' => 'bencana',
                'topic' => 'koordinasi-bantuan',
                'source_name' => 'BNPB - bnpb.go.id',
                'source_url' => 'https://www.bnpb.go.id/berita/bantuan-korban-bencana-sumbar-harus-terkoordinasi-melalui-posko',
                'source_date' => '2025-12-02',
                'content' => <<<'TEXT'
Pemprov Sumatera Barat meminta seluruh pihak berkoordinasi melalui posko sebelum menyalurkan bantuan agar tidak terjadi duplikasi. Bantuan ke 13 kabupaten/kota tetap wajib dicatat di posko setempat meski disalurkan secara mandiri. Contoh kebutuhan yang dilaporkan: Kabupaten Pesisir Selatan membutuhkan gergaji mesin, sembako, family kit, dan terpal.
TEXT,
            ],

            [
                'title' => 'Pembangunan Hunian Sementara (Huntara) — Empat Kabupaten Sumatera Barat',
                'category' => 'bencana',
                'topic' => 'pemulihan',
                'source_name' => 'Kementerian Koordinator PMK - kemenkopmk.go.id',
                'source_url' => 'https://www.kemenkopmk.go.id/menko-pmk-resmikan-hunian-sementara-bagi-masyarakat-terdampak-bencana-di-sumatra-barat',
                'source_date' => '2026-01-24',
                'content' => <<<'TEXT'
Sebanyak 273 unit hunian sementara (huntara) diresmikan di Kabupaten Agam, Padang Pariaman, Lima Puluh Kota, dan Pesisir Selatan, Sumatera Barat. Terkait Dana Tunggu Hunian (DTH): 2.279 KK diusulkan, 1.867 rekening disiapkan, dan 1.393 DTH telah tersalurkan.
TEXT,
            ],

            [
                'title' => 'Fase Pemulihan Pascabencana Sumatera — Mei 2026',
                'category' => 'bencana',
                'topic' => 'pemulihan',
                'source_name' => 'Kompas (mengutip Kemendagri)',
                'source_url' => 'https://kilaskementerian.kompas.com/kemendagri/read/2026/05/25/19005141/mendagri-pastikan-pemulihan-pascabencana-sumatera-masuk-tahap-pemulihan',
                'source_date' => '2026-05-25',
                'content' => <<<'TEXT'
Penanganan bencana di Aceh, Sumatera Utara, dan Sumatera Barat memasuki fase pemulihan permanen (rehabilitasi-rekonstruksi). Layanan dasar seperti listrik, BBM, rumah sakit, dan puskesmas sudah kembali normal. Anggaran rehab-rekon disetujui sekitar Rp100,1 triliun untuk tiga tahun; hunian tetap (huntap) ditargetkan selesai pada 2027.
TEXT,
            ],

            [
                'title' => 'Imbauan Kesiapsiagaan Kekeringan & Karhutla — 7 Juni 2026',
                'category' => 'bencana',
                'topic' => 'kesiapsiagaan',
                'source_name' => 'BNPB - bnpb.go.id',
                'source_url' => 'https://bnpb.go.id/berita/perkembangan-situasi-dan-penanganan-bencana-di-tanah-air-7-juni-2026',
                'source_date' => '2026-06-07',
                'content' => <<<'TEXT'
BPBD Bondowoso menyalurkan 150.000 liter air bersih ke 15 titik untuk 2.257 KK terdampak kekeringan. BNPB mengimbau pemerintah daerah dan masyarakat meningkatkan kesiapsiagaan menghadapi kebakaran hutan dan lahan (karhutla), mengaktifkan posko, serta menggelar patroli terpadu bersama TNI/Polri/relawan.
TEXT,
            ],

            [
                'title' => 'Portal Data Resmi untuk Pemantauan Bencana Real-time',
                'category' => 'bencana',
                'topic' => 'sumber-data',
                'source_name' => 'BNPB - data.bnpb.go.id',
                'source_url' => 'https://data.bnpb.go.id/',
                'source_date' => '2026-06-25',
                'content' => <<<'TEXT'
BNPB menyediakan Geoportal Data Bencana Indonesia (peta/visualisasi kejadian) dan Portal Satu Data Bencana Indonesia (dashboard penanganan per provinsi) sesuai Peraturan BNPB No. 7 Tahun 2023. Keduanya menjadi rujukan resmi untuk pemantauan kejadian dan penanganan bencana secara real-time.
TEXT,
            ],

            [
                'title' => 'Titik Terdampak: Mandailing Natal — Batahan IV (Akses Terbatas)',
                'category' => 'bencana',
                'topic' => 'titik-terdampak',
                'source_name' => 'BNPB - Portal Satu Data Bencana Indonesia',
                'source_url' => 'https://data.bnpb.go.id/dataset/9144c8ed-4c4d-43f5-acc7-c6486324f0ad/resource/8444ab39-70b3-4e24-ac0e-335308bb2170/download/titik-terdampak-dan-posko-pengungsian-bansor-sumatera-2025.xlsx',
                'source_date' => '2025-12-08',
                'content' => <<<'TEXT'
Wilayah terdampak banjir/longsor Sumatera 2025: Kabupaten Mandailing Natal, Kecamatan Batahan, Desa Batahan IV (koordinat sekitar 0.361261, 99.296290). Status akses dilaporkan TERBATAS. Data bersumber dari Portal Satu Data Bencana Indonesia (BNPB).
TEXT,
            ],

            [
                'title' => 'Titik Terdampak: Tapanuli Tengah — Desa Aek Bottar (Perlu Verifikasi Status Akses)',
                'category' => 'bencana',
                'topic' => 'titik-terdampak',
                'source_name' => 'BNPB - Portal Satu Data Bencana Indonesia',
                'source_url' => 'https://data.bnpb.go.id/dataset/9144c8ed-4c4d-43f5-acc7-c6486324f0ad/resource/8444ab39-70b3-4e24-ac0e-335308bb2170/download/titik-terdampak-dan-posko-pengungsian-bansor-sumatera-2025.xlsx',
                'source_date' => '2025-12-08',
                'content' => <<<'TEXT'
Wilayah terdampak: Kabupaten Tapanuli Tengah, Kecamatan Tukka, Desa Aek Bottar (koordinat sekitar 1.659167, 98.934722). Catatan kejujuran data: nilai status akses pada sumber tidak baku ("Akses terbatasterbuka") sehingga makna aslinya perlu verifikasi manual sebelum dijadikan acuan. Data bersumber dari Portal Satu Data Bencana Indonesia (BNPB).
TEXT,
            ],

            [
                'title' => 'Akses Terputus: Jalan Padang–Bukittinggi via Malalak (Agam)',
                'category' => 'bencana',
                'topic' => 'akses-jalan',
                'source_name' => 'BNPB - Portal Satu Data Bencana Indonesia',
                'source_url' => 'https://data.bnpb.go.id/dataset/9144c8ed-4c4d-43f5-acc7-c6486324f0ad/resource/8444ab39-70b3-4e24-ac0e-335308bb2170/download/titik-terdampak-dan-posko-pengungsian-bansor-sumatera-2025.xlsx',
                'source_date' => '2025-12-08',
                'content' => <<<'TEXT'
Ruas jalan akses Padang–Bukittinggi via Malalak (Kabupaten Agam, Kecamatan Malalak, Malalak Timur; koordinat sekitar -0.403710, 100.280000) dilaporkan TIDAK DAPAT DIAKSES akibat dampak bencana. Pengguna jalan diimbau mencari jalur alternatif dan mengikuti arahan petugas. Data bersumber dari Portal Satu Data Bencana Indonesia (BNPB).
TEXT,
            ],

            [
                'title' => 'Akses Terputus: Jembatan Tenge Besi (Bener Meriah)',
                'category' => 'bencana',
                'topic' => 'akses-jalan',
                'source_name' => 'BNPB - Portal Satu Data Bencana Indonesia',
                'source_url' => 'https://data.bnpb.go.id/dataset/9144c8ed-4c4d-43f5-acc7-c6486324f0ad/resource/8444ab39-70b3-4e24-ac0e-335308bb2170/download/titik-terdampak-dan-posko-pengungsian-bansor-sumatera-2025.xlsx',
                'source_date' => '2025-12-08',
                'content' => <<<'TEXT'
Jembatan Tenge Besi di Kabupaten Bener Meriah (Kecamatan Gajah Putih, Desa Reronga; koordinat sekitar 4.826090, 96.748135) dilaporkan TIDAK DAPAT DIAKSES akibat dampak bencana. Warga diimbau tidak memaksakan melintas dan mengikuti jalur alternatif yang diarahkan petugas. Data bersumber dari Portal Satu Data Bencana Indonesia (BNPB).
TEXT,
            ],
        ];

        foreach ($documents as $data) {
            KnowledgeDocument::create(array_merge($data, ['is_active' => true]));
        }

        $this->command->info('Seeded '.count($documents).' knowledge documents (real, source-traceable).');
    }
}
