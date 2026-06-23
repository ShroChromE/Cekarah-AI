<?php

namespace Database\Seeders;

use App\Models\KnowledgeDocument;
use Illuminate\Database\Seeder;

class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [

            // ═══════════════════════════════════════════════
            // KELOMPOK A — DARURAT & EVAKUASI (6 dokumen)
            // ═══════════════════════════════════════════════

            [
                'title' => 'Prosedur Evakuasi Banjir: Langkah demi Langkah',
                'category' => 'prosedur',
                'topic' => 'evakuasi',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan SOP BNPB)',
                'source_url' => 'sintetis://cekarah-team/prosedur-evakuasi-banjir',
                'source_date' => '2025-01-01',
                'content' => <<<'TEXT'
Ketika banjir datang, ikuti prosedur evakuasi berikut secara berurutan:

LANGKAH 1 — Kenali tanda peringatan. Perhatikan sirine BPBD, pengumuman dari RT/RW, atau notifikasi dari aplikasi Info BMKG. Jangan tunggu air masuk rumah baru bergerak.

LANGKAH 2 — Matikan listrik dan gas. Cabut semua peralatan elektronik dari stop kontak. Tutup keran gas utama. Ini mencegah kebakaran dan sengatan listrik saat banjir.

LANGKAH 3 — Ambil tas darurat. Pastikan tas berisi dokumen penting (dalam plastik kedap air), obat-obatan, pakaian ganti 3 hari, uang tunai, senter, dan powerbank.

LANGKAH 4 — Evakuasi anggota keluarga rentan terlebih dahulu. Utamakan lansia, bayi, ibu hamil, dan penyandang disabilitas. Minta bantuan tetangga jika perlu.

LANGKAH 5 — Gunakan jalur evakuasi yang sudah ditentukan. Hindari melintasi air yang mengalir deras — ketinggian 15 cm sudah bisa menghanyutkan orang dewasa.

LANGKAH 6 — Pergi ke titik kumpul atau tempat pengungsian resmi. Laporkan kehadiran ke petugas pengungsian agar terdata sebagai penyintas.

HAL YANG DILARANG DILAKUKAN:
- Jangan kembali ke rumah untuk mengambil barang saat air masih naik
- Jangan mengemudi melalui jalan yang tergenang (kedalaman tidak bisa diprediksi)
- Jangan gunakan lift saat banjir
- Jangan sentuh tiang listrik atau kabel yang jatuh ke air

Hubungi BPBD setempat atau BNPB di 117 ext 7 jika membutuhkan bantuan evakuasi.
TEXT,
            ],

            [
                'title' => 'Daftar Dokumen Wajib Dibawa Saat Evakuasi Bencana',
                'category' => 'prosedur',
                'topic' => 'evakuasi',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan panduan BNPB dan Dukcapil)',
                'source_url' => 'sintetis://cekarah-team/dokumen-evakuasi',
                'source_date' => '2025-01-01',
                'content' => <<<'TEXT'
Dokumen penting yang WAJIB dibawa saat evakuasi bencana. Simpan semua dalam plastik ziplock atau kantong kedap air berlapis dua:

DOKUMEN PRIORITAS UTAMA (tidak bisa diganti jika hilang):
1. KTP (Kartu Tanda Penduduk) — untuk semua anggota keluarga di atas 17 tahun
2. Kartu Keluarga (KK)
3. Akta Kelahiran anak-anak
4. Paspor (jika ada)
5. Buku Nikah / Akta Perkawinan

DOKUMEN PENTING KEDUA:
6. Buku tabungan dan kartu ATM
7. BPJS Kesehatan dan kartu asuransi lain
8. Sertifikat tanah/rumah (fotokopi cukup)
9. STNK dan BPKB kendaraan
10. Ijazah terakhir (asli jika mungkin)

TIPS PENYIMPANAN:
- Buat fotokopi dan simpan terpisah dari dokumen asli
- Upload foto digital ke penyimpanan cloud (Google Photos, iCloud)
- Catat nomor KTP dan KK di ponsel — berguna jika dokumen hilang

Jika dokumen hilang akibat bencana, segera lapor ke Dinas Dukcapil setempat setelah kondisi aman. Proses penggantian dokumen korban bencana biasanya dipercepat dan digratiskan.
TEXT,
            ],

            [
                'title' => 'Cara Lapor ke Posko BNPB dan Prosedur Mendapat Logistik Darurat',
                'category' => 'prosedur',
                'topic' => 'bantuan-darurat',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan SOP BNPB 117)',
                'source_url' => 'sintetis://cekarah-team/lapor-posko-bnpb',
                'source_date' => '2025-06-01',
                'content' => <<<'TEXT'
Cara menghubungi BNPB dan mendapatkan logistik darurat saat bencana:

KONTAK UTAMA BNPB:
- Call Center: 117 ext 7 (aktif 24 jam, gratis dari semua operator)
- SMS/WhatsApp: bisa ditanyakan ke call center
- Website: bnpb.go.id
- BPBD Daerah: hubungi kantor BPBD kabupaten/kota setempat

PROSEDUR LAPOR KE POSKO:
1. Hubungi 117 ext 7 dan sampaikan: nama lengkap, lokasi (koordinat GPS jika bisa), jumlah orang yang membutuhkan bantuan, kondisi darurat yang dialami
2. Petugas akan mencatat dan meneruskan ke BPBD terdekat
3. Dapatkan nomor tiket pelaporan — simpan untuk follow-up

LOGISTIK DARURAT YANG BISA DIMINTA:
- Makanan siap saji dan air minum
- Selimut dan pakaian darurat
- Obat-obatan dasar (P3K)
- Tenda pengungsian
- Alat komunikasi darurat

DI POSKO PENGUNGSIAN:
- Daftarkan seluruh anggota keluarga ke petugas pendataan
- Informasikan kebutuhan khusus: bayi, ibu hamil, lansia, penyandang disabilitas, pasien sakit
- Minta kartu pengungsi sebagai bukti pendataan — berguna untuk klaim bantuan sosial selanjutnya

Jika tidak bisa menelepon, kirim pesan teks singkat ke 117 dengan format: BENCANA_NAMA_LOKASI_JUMLAH_ORANG
TEXT,
            ],

            [
                'title' => 'Prosedur Pencarian Orang Hilang via Basarnas',
                'category' => 'prosedur',
                'topic' => 'orang-hilang',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan prosedur Basarnas)',
                'source_url' => 'sintetis://cekarah-team/prosedur-basarnas-orang-hilang',
                'source_date' => '2025-01-01',
                'content' => <<<'TEXT'
Jika ada anggota keluarga atau orang yang hilang akibat bencana, lakukan langkah-langkah berikut:

KONTAK BASARNAS:
- Pusat: 115 (aktif 24 jam)
- Kantor SAR terdekat: cek di basarnas.go.id/tentang-kami/kantor-sar
- Email: info@basarnas.go.id

INFORMASI YANG HARUS DISIAPKAN SEBELUM MENELEPON:
1. Nama lengkap orang hilang
2. Usia dan ciri fisik (tinggi, berat, ciri khas seperti tato, tahi lalat)
3. Pakaian terakhir yang dikenakan
4. Lokasi terakhir diketahui — semakin spesifik semakin baik
5. Waktu terakhir terlihat
6. Nama pelapor dan nomor yang bisa dihubungi

PROSES PENCARIAN:
1. Laporan diterima dan diverifikasi oleh tim Basarnas
2. Tim SAR dikirim ke lokasi berdasarkan prioritas dan ketersediaan sumber daya
3. Koordinasi dengan BPBD, TNI, Polri, dan relawan untuk area luas
4. Update situasi diberikan secara berkala kepada pelapor

TIPS PENTING:
- Laporkan secepat mungkin — semakin cepat laporan, semakin besar kemungkinan ditemukan
- Bagikan foto terbaru orang hilang ke media sosial dengan tagar #SARHelp dan lokasi bencana
- Cek juga di pos pengungsian resmi — korban mungkin sudah dievakuasi tapi belum terdata
- Jangan masuk ke zona berbahaya sendiri untuk mencari — serahkan ke tim profesional

Basarnas memiliki personel, helikopter, kapal, dan anjing pelacak khusus SAR.
TEXT,
            ],

            [
                'title' => 'Fasilitas yang Tersedia di Tempat Pengungsian Resmi',
                'category' => 'prosedur',
                'topic' => 'pengungsian',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan standar Sphere dan BNPB)',
                'source_url' => 'sintetis://cekarah-team/fasilitas-pengungsian',
                'source_date' => '2025-01-01',
                'content' => <<<'TEXT'
Tempat pengungsian resmi yang dikelola BNPB/BPBD harus memenuhi standar minimum berikut:

FASILITAS DASAR YANG WAJIB ADA:
1. Tempat tidur/alas tidur dan selimut (1 set per orang)
2. Air bersih minimal 15 liter per orang per hari
3. Toilet — standar 1 toilet untuk 20 orang, dipisah pria dan wanita
4. Makanan 3 kali sehari (kalori minimum 2.100 kkal/orang/hari)
5. Penerangan listrik di area umum

FASILITAS KESEHATAN:
- Pos kesehatan dengan tenaga medis (dokter atau perawat)
- Obat-obatan dasar tersedia
- Layanan kesehatan jiwa/psikososial (untuk trauma)
- Layanan khusus: ibu hamil, bayi (MPASI, ASI), lansia

FASILITAS KHUSUS:
- Ruang laktasi untuk ibu menyusui (tertutup/privat)
- Area bermain anak yang aman
- Aksesibilitas untuk penyandang disabilitas
- Penerima manfaat BPJS Kesehatan tetap bisa berobat gratis

HAK PENGUNGSI:
- Berhak mendapat kartu identitas pengungsi
- Berhak mendapat informasi tentang kondisi bencana dan rencana pemulangan
- Berhak melaporkan keluhan ke petugas BPBD

Jika fasilitas tidak terpenuhi, laporkan ke BNPB 117 ext 7 atau Kemensos 1500771.
TEXT,
            ],

            [
                'title' => 'Direktori Kontak Darurat Resmi Bencana Indonesia',
                'category' => 'prosedur',
                'topic' => 'kontak-darurat',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan data resmi lembaga)',
                'source_url' => 'sintetis://cekarah-team/direktori-kontak-darurat',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Daftar kontak darurat resmi yang bisa dihubungi saat bencana di Indonesia:

LEMBAGA PENANGGULANGAN BENCANA:
- BNPB (Badan Nasional Penanggulangan Bencana): 117 ext 7 — 24 jam
- BPBD (Badan Penanggulangan Bencana Daerah): hubungi BPBD kabupaten/kota setempat
- Basarnas (Search and Rescue): 115 — 24 jam

LEMBAGA KESEHATAN DAN SOSIAL:
- PMI (Palang Merah Indonesia): 021-7992325
- Kemensos (Kementerian Sosial) — Hotline Bantuan Sosial: 1500771
- BPJS Kesehatan: 165 (informasi layanan saat darurat)
- Kemenkes — hotline: 119 ext 8

INFORMASI KEBENCANAAN:
- BMKG (cuaca, gempa, tsunami): 021-6546318 atau bmkg.go.id
- Pusat Vulkanologi: magma.esdm.go.id (gunung berapi)

KEAMANAN:
- Polisi: 110
- Pemadam Kebakaran: 113
- Ambulans: 118 atau 119

VERIFIKASI INFORMASI:
- aduankonten.id (Kemkominfo) — lapor hoaks
- bnpb.go.id — berita resmi bencana
- bmkg.go.id — informasi cuaca dan gempa resmi

Simpan nomor-nomor ini di ponsel sebelum bencana terjadi. Dalam kondisi darurat, ingat tiga angka utama: BNPB 117, Basarnas 115, PMI 021-7992325.
TEXT,
            ],

            // ═══════════════════════════════════════════════
            // KELOMPOK B — BANTUAN SOSIAL PASCA BENCANA (7 dokumen)
            // ═══════════════════════════════════════════════

            [
                'title' => 'Cara Daftar Bantuan Darurat Lewat Aplikasi Cek Bansos',
                'category' => 'bantuan',
                'topic' => 'registrasi-bansos',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan panduan Kemensos)',
                'source_url' => 'sintetis://cekarah-team/daftar-cek-bansos-app',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Cara mendaftar bantuan sosial darurat melalui aplikasi Cek Bansos milik Kementerian Sosial:

LANGKAH 1 — Unduh Aplikasi
Download "Cek Bansos" di Google Play Store (Android) atau App Store (iOS). Pastikan mengunduh dari akun resmi Kemensos.

LANGKAH 2 — Buat Akun
- Buka aplikasi, pilih "Daftar"
- Masukkan NIK (Nomor Induk Kependudukan) dari KTP
- Masukkan nomor Kartu Keluarga
- Upload foto KTP dan foto selfie dengan KTP
- Tunggu verifikasi (biasanya 1x24 jam, dipercepat saat bencana)

LANGKAH 3 — Ajukan Usulan Bantuan
- Setelah akun aktif, pilih "Usul Bansos"
- Pilih kategori: "Korban Bencana"
- Isi formulir: nama, alamat terdampak, jenis bantuan dibutuhkan
- Upload bukti: foto kondisi rumah/bencana jika memungkinkan
- Submit dan catat nomor pengajuan

LANGKAH 4 — Pantau Status
- Buka menu "Status Bansos"
- Masukkan NIK atau nomor pengajuan
- Status akan berubah: Diajukan → Diverifikasi → Disetujui → Disalurkan

CATATAN PENTING:
- Jika tidak punya smartphone, minta bantuan ke Pendamping PKH atau Kepala Desa
- Data NIK harus valid sesuai Dukcapil — pastikan KTP tidak kadaluarsa
- Dalam kondisi darurat, proses verifikasi bisa dipercepat menjadi 3-5 hari

Untuk bantuan: hubungi 1500771 (Kemensos) atau datang langsung ke Dinas Sosial setempat.
TEXT,
            ],

            [
                'title' => 'Pendaftaran Bantuan Sosial Offline Lewat Kantor Desa/Kelurahan',
                'category' => 'bantuan',
                'topic' => 'registrasi-bansos',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan prosedur Kemensos)',
                'source_url' => 'sintetis://cekarah-team/daftar-bansos-offline',
                'source_date' => '2025-01-01',
                'content' => <<<'TEXT'
Bagi yang tidak memiliki smartphone atau akses internet, pendaftaran bantuan sosial bisa dilakukan secara offline:

JALUR OFFLINE — MELALUI DESA/KELURAHAN:

1. Datang ke kantor Desa atau Kelurahan tempat tinggal terdampak
2. Minta formulir "Usulan Penerima Bantuan Sosial" atau "Form DTSEN"
3. Isi formulir dengan lengkap — minta bantuan petugas jika kesulitan
4. Lampirkan dokumen: fotokopi KTP, fotokopi KK, dan surat keterangan terdampak bencana (dari RT/RW atau BPBD)
5. Serahkan ke petugas Desa/Kelurahan — minta tanda terima

PETUGAS YANG BISA MEMBANTU:
- Kepala Desa / Lurah
- Pendamping PKH (Program Keluarga Harapan) di wilayah setempat
- Pendamping Sosial Kemensos
- Petugas Dinas Sosial kabupaten/kota

ALUR SELANJUTNYA:
1. Data dikirim dari Desa ke Dinas Sosial kabupaten/kota
2. Dinas Sosial memverifikasi dan mengusulkan ke Kemensos
3. Kemensos memproses dalam DTSEN (Data Tunggal Sosial Ekonomi Nasional)
4. Jika disetujui, bantuan disalurkan lewat bank Himbara (BRI, BNI, Mandiri, BTN)

CATATAN UNTUK KORBAN BENCANA:
- Sertakan surat keterangan bencana dari BPBD — mempercepat proses
- Jika tidak ada dokumen karena hilang saat bencana, lapor dulu ke Dukcapil, minta surat keterangan pengganti
- Proses normal 2-4 minggu, tapi bisa dipercepat 1 minggu untuk situasi darurat bencana

Hubungi Kemensos 1500771 untuk cek status atau pertanyaan.
TEXT,
            ],

            [
                'title' => 'Syarat Penerima DTSEN 2026: Siapa yang Berhak Mendapat Bantuan Sosial',
                'category' => 'bantuan',
                'topic' => 'syarat-bansos',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan Permensos dan DTSEN 2026)',
                'source_url' => 'sintetis://cekarah-team/syarat-dtsen-2026',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
DTSEN (Data Tunggal Sosial Ekonomi Nasional) 2026 adalah basis data penerima bantuan sosial Indonesia. Berikut syarat untuk menjadi penerima manfaat:

SYARAT UMUM PENERIMA:
1. Warga Negara Indonesia (WNI) yang memiliki NIK aktif di Dukcapil
2. Terdaftar dalam Data Kependudukan yang valid
3. Masuk dalam kelompok desil 1-4 (40% masyarakat dengan kondisi sosial-ekonomi terendah)
4. Bukan Aparatur Sipil Negara (ASN) aktif, TNI, atau Polri aktif
5. Bukan anggota keluarga pejabat negara
6. Tidak memiliki aset atau pendapatan yang melebihi batas kemampuan

KELOMPOK PRIORITAS (mendapat percepatan proses):
- Keluarga miskin dengan anak balita atau ibu hamil
- Keluarga dengan anggota penyandang disabilitas berat
- Keluarga dengan lansia di atas 70 tahun tanpa penghasilan
- Korban bencana alam yang kehilangan tempat tinggal

YANG TIDAK BERHAK:
- PNS/TNI/Polri dan keluarga intinya
- Pengusaha dengan omzet di atas batas tertentu
- Pemilik lebih dari 1 unit properti (dikecualikan untuk kasus tertentu)
- Penerima gaji/pensiun formal di atas UMP

CEK APAKAH SUDAH TERDAFTAR:
1. Buka cekbansos.kemensos.go.id
2. Masukkan NIK dan nama sesuai KTP
3. Jika belum terdaftar padahal memenuhi syarat, ajukan melalui aplikasi Cek Bansos atau kantor Desa

Untuk sanggah atau pertanyaan: 1500771 (Kemensos)
TEXT,
            ],

            [
                'title' => 'Cara Cek Status Penerima Bantuan Sosial di cekbansos.kemensos.go.id',
                'category' => 'bantuan',
                'topic' => 'cek-status',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan panduan Kemensos)',
                'source_url' => 'sintetis://cekarah-team/cek-status-bansos',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Cara memeriksa apakah nama Anda atau keluarga terdaftar sebagai penerima bantuan sosial:

CARA 1 — MELALUI WEBSITE (paling mudah):
1. Buka browser, akses: cekbansos.kemensos.go.id
2. Pilih Provinsi, Kabupaten/Kota, Kecamatan, Desa/Kelurahan
3. Masukkan Nama sesuai KTP (huruf besar semua)
4. Klik "CARI DATA"
5. Jika terdaftar, akan muncul nama, program bansos yang diterima, dan status penyaluran

CARA 2 — MELALUI APLIKASI CEK BANSOS:
1. Buka aplikasi Cek Bansos
2. Login dengan akun yang sudah dibuat
3. Pilih menu "Status Bansos"
4. Data penerimaan akan muncul secara otomatis sesuai NIK

CARA 3 — MELALUI KANTOR POS / BANK HIMBARA:
- Tunjukkan KTP ke petugas
- Petugas bisa mengecek apakah ada jadwal penyaluran untuk nama Anda
- Bank: BRI, BNI, Mandiri, BTN adalah bank penyalur resmi

MEMAHAMI STATUS YANG MUNCUL:
- "Aktif / Layak" = terdaftar dan akan menerima
- "Non-Aktif" = sempat terdaftar tapi dikeluarkan (cek alasan ke Dinsos)
- "Tidak Ditemukan" = belum terdaftar atau nama tidak sesuai (coba variasi penulisan nama)

JIKA DATA TIDAK MUNCUL TAPI MERASA LAYAK:
Ajukan usulan melalui Desa/Kelurahan atau aplikasi Cek Bansos. Proses verifikasi biasanya 14 hari kerja.

Hotline: 1500771 (Kemensos)
TEXT,
            ],

            [
                'title' => 'Alur Pendaftaran PKH untuk Korban Bencana',
                'category' => 'bantuan',
                'topic' => 'pkh',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan Pedoman PKH Kemensos 2026)',
                'source_url' => 'sintetis://cekarah-team/pkh-korban-bencana',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
PKH (Program Keluarga Harapan) adalah bantuan tunai bersyarat dari Kemensos. Korban bencana yang belum terdaftar bisa mengajukan kepesertaan darurat.

SYARAT PKH:
- Keluarga miskin yang memiliki: ibu hamil, anak usia 0-6 tahun, anak SD-SMA, anggota keluarga disabilitas berat, atau lansia 70 tahun ke atas
- Belum menerima PKH sebelumnya
- Masuk desil 1-4 DTSEN

BESARAN BANTUAN PKH 2026 (per tahun):
- Ibu hamil: Rp 3.000.000
- Anak usia dini (0-6 tahun): Rp 3.000.000
- Anak SD: Rp 900.000
- Anak SMP: Rp 1.500.000
- Anak SMA: Rp 2.000.000
- Penyandang disabilitas berat: Rp 2.400.000
- Lansia 70 tahun ke atas: Rp 2.400.000

DOKUMEN YANG DIBUTUHKAN:
1. KTP dan KK (asli + fotokopi)
2. Surat keterangan tidak mampu dari desa (atau surat keterangan bencana)
3. Dokumen pendukung sesuai komponen: buku KIA untuk balita, kartu sekolah untuk anak, surat keterangan disabilitas dari RS untuk penyandang disabilitas

PROSES PENDAFTARAN KORBAN BENCANA:
1. Datang ke Pendamping PKH di desa atau Dinas Sosial
2. Nyatakan status sebagai korban bencana dan minta jalur pendaftaran darurat
3. Lengkapi dokumen yang diminta
4. Pendamping akan menginput data ke SIKS-NG (Sistem Informasi Kesejahteraan Sosial)
5. Proses verifikasi 14-30 hari, bisa dipercepat untuk bencana

Cari Pendamping PKH di wilayahmu: hubungi Dinas Sosial atau Kemensos 1500771.
TEXT,
            ],

            [
                'title' => 'BPNT (Bantuan Pangan Non-Tunai): Mekanisme dan Cara Mendapatkannya',
                'category' => 'bantuan',
                'topic' => 'bpnt',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan Permensos BPNT 2026)',
                'source_url' => 'sintetis://cekarah-team/bpnt-mekanisme',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
BPNT (Bantuan Pangan Non-Tunai) adalah program bantuan pangan yang disalurkan melalui rekening bank untuk membeli bahan pangan di e-Warong.

BESARAN BPNT 2026:
- Rp 200.000 per keluarga per bulan
- Disalurkan setiap bulan ke rekening KKS (Kartu Keluarga Sejahtera)

CARA MENDAPATKAN BPNT SEBAGAI KORBAN BENCANA:
1. Pastikan terdaftar di DTSEN — cek di cekbansos.kemensos.go.id
2. Jika belum terdaftar, ajukan melalui desa/kelurahan atau aplikasi Cek Bansos
3. Setelah disetujui, KKS akan diterbitkan oleh bank Himbara (BRI/BNI/Mandiri/BTN)

CARA BELANJA DENGAN KKS:
1. Datang ke e-Warong (agen bank atau toko yang ditunjuk Kemensos) terdekat
2. Tunjukkan KKS dan verifikasi dengan sidik jari atau PIN
3. Pilih produk yang termasuk dalam BPNT: beras, telur, kacang-kacangan, sayuran, buah, ayam/ikan, tahu/tempe
4. Saldo akan terpotong sesuai belanjaan

MENEMUKAN E-WARONG TERDEKAT:
- Tanya ke RT/RW atau Desa
- Atau minta informasi ke Dinas Sosial setempat

JIKA KKS HILANG SAAT BENCANA:
1. Lapor ke bank penerbit (BRI/BNI/Mandiri/BTN)
2. Bawa surat keterangan kehilangan dari polisi atau surat bencana dari BPBD
3. Proses penggantian kartu biasanya 7-14 hari kerja, dipercepat untuk korban bencana

Hotline Kemensos: 1500771
TEXT,
            ],

            [
                'title' => 'Cara Usul Sanggah Desil DTSEN: Jika Data Ekonomi Tidak Sesuai Kondisi Nyata',
                'category' => 'bantuan',
                'topic' => 'sanggah-dtsen',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan mekanisme DTSEN Kemensos 2026)',
                'source_url' => 'sintetis://cekarah-team/sanggah-desil-dtsen',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Jika kondisi ekonomi keluargamu lebih buruk dari yang tercatat di DTSEN (misalnya karena baru bangkrut, sakit keras, atau kehilangan pekerjaan akibat bencana), kamu bisa mengajukan sanggah.

APA ITU DESIL DTSEN?
Desil adalah pengelompokan 1-10 berdasarkan kondisi ekonomi. Desil 1 = paling miskin, desil 10 = paling kaya. Program bansos menyasar desil 1-4.

KAPAN PERLU SANGGAH?
- Terdaftar di desil 5 ke atas tapi kondisi nyata masuk desil 1-4
- Baru mengalami musibah besar: bencana, sakit keras, PHK, kematian pencari nafkah
- Data yang tercatat sudah tidak relevan (misalnya dari sensus beberapa tahun lalu)

CARA USUL SANGGAH:

Jalur 1 — Melalui Aplikasi Cek Bansos:
1. Login ke aplikasi Cek Bansos
2. Pilih menu "Usul Bansos" atau "Sanggah Data"
3. Isi formulir: alasan sanggah, kondisi terkini, unggah bukti
4. Submit — catat nomor pengajuan

Jalur 2 — Melalui Desa/Kelurahan:
1. Datang ke kantor desa/kelurahan
2. Minta formulir USUL/SANGGAH DTSEN
3. Lampirkan surat keterangan dari RT/RW tentang kondisi ekonomi
4. Lampirkan bukti bencana jika relevan: foto rumah rusak, surat BPBD
5. Serahkan ke petugas — minta tanda terima

DOKUMEN PENDUKUNG YANG MEMPERKUAT SANGGAH:
- Surat keterangan bencana dari BPBD
- Surat keterangan tidak bekerja dari kelurahan
- Tagihan listrik (bukti golongan daya kecil = 450/900 VA)
- Foto kondisi rumah terdampak bencana

WAKTU PROSES: 30-60 hari untuk sanggah reguler, bisa dipercepat untuk korban bencana.

Hotline: 1500771 (Kemensos) | cekbansos.kemensos.go.id
TEXT,
            ],

            // ═══════════════════════════════════════════════
            // KELOMPOK C — VERIFIKASI HOAKS BENCANA (7 dokumen)
            // ═══════════════════════════════════════════════

            [
                'title' => 'Pola Hoaks "Air Laut Naik / Tsunami Palsu" dan Cara Verifikasi ke BMKG',
                'category' => 'verifikasi',
                'topic' => 'hoaks-tsunami',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan kajian hoaks BMKG dan Kemkominfo)',
                'source_url' => 'sintetis://cekarah-team/hoaks-tsunami-verifikasi',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Hoaks tentang tsunami atau kenaikan air laut adalah salah satu jenis hoaks bencana yang paling berbahaya karena memicu kepanikan massal dan mengganggu operasi SAR.

POLA KHAS HOAKS TSUNAMI:
1. Pesan berantai WhatsApp tanpa sumber resmi: "Info dari teman di [lokasi], air laut naik, segera menjauh!"
2. Video atau foto lama dari bencana berbeda yang diklaim terjadi "sekarang"
3. Klaim "dari tetangga", "dari polisi", "dari tentara" tanpa identitas jelas
4. Urgensi berlebihan: "SEGERA SHARE!" atau "JANGAN DISIMPAN SENDIRI!"
5. Tidak menyebutkan sumber resmi (BMKG, BNPB, atau Pemda)

CARA VERIFIKASI YANG BENAR:
1. Buka bmkg.go.id atau aplikasi Info BMKG — ada fitur "Peringatan Dini Tsunami"
2. Pantau akun resmi: @InfoBMKG di Twitter/X dan Instagram
3. Hubungi BMKG: 021-6546318
4. Cek bnpb.go.id untuk peta situasi bencana aktif
5. Tonton TVRI atau RRI — stasiun ini wajib menyiarkan peringatan resmi

PERINGATAN TSUNAMI RESMI MEMILIKI CIRI:
- Dikeluarkan oleh BMKG secara resmi
- Ada nomor gempa dan magnitudo yang memicunya
- Ada level peringatan: AWAS (1), SIAGA (2), WASPADA (3), atau TIDAK ADA ANCAMAN
- Disebarkan melalui sirine, Indonesia Tsunami Early Warning System (InaTEWS)

JIKA TIDAK ADA INFORMASI DI SUMBER RESMI:
Hampir pasti itu tidak benar. BMKG tidak pernah telat dalam mengumumkan ancaman tsunami jika memang ada.

Laporkan hoaks ke: aduankonten.id
TEXT,
            ],

            [
                'title' => 'Pola Hoaks "Gempa Susulan Berbahaya" via Pesan Berantai',
                'category' => 'verifikasi',
                'topic' => 'hoaks-gempa',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan panduan BMKG dan Kemkominfo)',
                'source_url' => 'sintetis://cekarah-team/hoaks-gempa-susulan',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Setelah gempa besar, sering beredar hoaks tentang gempa susulan yang diklaim "lebih besar" atau "merusak". Ini sangat berbahaya karena membuat korban takut kembali ke rumah yang sebenarnya aman.

POLA HOAKS GEMPA SUSULAN:
1. "Akan ada gempa susulan M 7+ dalam 24 jam ke depan" — prediksi gempa tidak bisa dilakukan dengan presisi seperti ini
2. "Info dari BMKG: jauhi pantai selama 3 hari" — BMKG tidak pernah memberi instruksi sewenang-wenang tanpa data
3. Tangkapan layar artikel lama dengan tanggal diubah
4. Video "retakan tanah" yang diklaim dari lokasi bencana terkini tapi sebenarnya dari tempat lain

FAKTA ILMIAH TENTANG GEMPA:
- Gempa tidak bisa diprediksi tanggal dan jamnya — ilmu pengetahuan saat ini belum mampu
- Gempa susulan biasanya lebih kecil dari gempa utama
- Jika ada ancaman tsunami, BMKG akan memberi peringatan resmi dalam menit

CARA VERIFIKASI BERITA GEMPA:
1. Buka bmkg.go.id → klik "Gempabumi Terkini"
2. Data gempa diperbarui real-time dengan magnitudo, kedalaman, dan lokasi
3. Aplikasi Info BMKG juga menampilkan data ini dengan notifikasi push
4. Untuk gempa di bawah M 5.0 biasanya tidak berpotensi tsunami

PERTANYAAN YANG HARUS DIAJUKAN SAAT MENERIMA INFO GEMPA:
- Apakah ada di bmkg.go.id atau bnpb.go.id?
- Siapa yang membuat informasi ini? Akun resmi atau perorangan tidak dikenal?
- Apakah ada tanggal dan waktu yang spesifik dengan sumber data?

Laporkan informasi menyesatkan ke: aduankonten.id atau akun media sosial BMKG.
TEXT,
            ],

            [
                'title' => 'Pola Hoaks Nomor Rekening Donasi Palsu saat Bencana',
                'category' => 'verifikasi',
                'topic' => 'hoaks-donasi',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan laporan Kemkominfo dan PPATK)',
                'source_url' => 'sintetis://cekarah-team/hoaks-rekening-donasi-palsu',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Setiap kali terjadi bencana besar, muncul ratusan rekening donasi palsu yang mengatasnamakan korban atau lembaga resmi. Ini adalah penipuan berkedok simpati.

POLA REKENING DONASI PALSU:
1. Nomor rekening atas nama perorangan, bukan lembaga — "Rek BRI a.n. Budi Santoso untuk korban banjir"
2. Akun media sosial baru dengan sedikit pengikut yang tiba-tiba menggalang dana
3. Mengklaim afiliasi dengan BNPB, PMI, atau lembaga resmi tapi tidak ada buktinya
4. Tidak ada nomor NPWP lembaga, tidak ada laporan keuangan transparan
5. Tekanan untuk "segera transfer sebelum terlambat"

CARA MEMVERIFIKASI DONASI YANG SAH:
1. Cek apakah lembaga terdaftar di website resmi: pmi.or.id, bnpb.go.id, atau Badan Amil Zakat
2. Rekening donasi resmi BIASANYA atas nama lembaga, bukan perorangan
3. Lembaga resmi mencantumkan NPWP dan laporan keuangan transparan
4. Cari di Google: nama lembaga + "rekening donasi resmi" untuk verifikasi

SALURAN DONASI RESMI YANG TERVERIFIKASI:
- PMI: pmi.or.id — rekening atas nama PMI Pusat
- BNPB: bnpb.go.id — selalu ada link resmi saat bencana besar
- Baznas: baznas.go.id
- LAZ (Lembaga Amil Zakat) terdaftar di Kemenag

JIKA SUDAH TERLANJUR TRANSFER KE REKENING MENCURIGAKAN:
1. Segera lapor ke bank: minta pemblokiran dan pelacakan transaksi
2. Lapor ke Polri: lapor.polri.go.id atau Bareskrim 021-7218011
3. Lapor ke PPATK: 021-3850455

Ingat: donasi yang baik harus ke lembaga yang bisa dipertanggungjawabkan.
TEXT,
            ],

            [
                'title' => 'Cara Membedakan Pengumuman BNPB/BMKG Resmi versus Hoaks',
                'category' => 'verifikasi',
                'topic' => 'verifikasi-pengumuman',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan panduan literasi digital Kemkominfo)',
                'source_url' => 'sintetis://cekarah-team/bedakan-pengumuman-resmi-hoaks',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Pengumuman resmi dari BNPB dan BMKG memiliki ciri yang bisa dibedakan dari hoaks. Kenali perbedaannya sebelum menyebarkan informasi.

CIRI PENGUMUMAN RESMI BNPB:
✅ Dikeluarkan di bnpb.go.id atau akun resmi @BNPB_Indonesia (Twitter/X, Instagram)
✅ Ada kop surat resmi jika berbentuk dokumen
✅ Ada nama dan jabatan pejabat yang menandatangani
✅ Ada tanggal dan nomor surat resmi
✅ Bahasa formal dan terstruktur
✅ Tidak meminta transfer uang atau data pribadi

CIRI PENGUMUMAN RESMI BMKG:
✅ Ada di bmkg.go.id atau aplikasi Info BMKG
✅ Data gempa mencantumkan: waktu UTC dan WIB, koordinat, magnitudo, kedalaman
✅ Ada status potensi tsunami dengan level yang jelas
✅ Ditandatangani Kepala BMKG atau pejabat yang ditunjuk

TANDA-TANDA HOAKS:
❌ Disebarkan lewat WhatsApp tanpa link ke sumber resmi
❌ "Info dari orang dalam", "bocoran dari petugas" tanpa nama jelas
❌ Foto/video tanpa metadata waktu dan lokasi yang bisa diverifikasi
❌ Kalimat yang memancing emosi: "SEBARKAN SEBELUM DIHAPUS!"
❌ Mengklaim informasi "eksklusif" sebelum media massa
❌ Terjemahan dari bahasa asing tentang "ancaman Indonesia" yang tidak ada di media lokal

LANGKAH VERIFIKASI 5 MENIT:
1. Cari di Google berita terkait — jika tidak ada di media nasional, curigai
2. Buka bmkg.go.id atau bnpb.go.id langsung — jangan lewat link di WhatsApp
3. Cek tanggal: banyak hoaks menggunakan berita lama yang disebarkan ulang

Laporkan konten yang terindikasi hoaks ke: aduankonten.id
TEXT,
            ],

            [
                'title' => 'Saluran Resmi Verifikasi Informasi Bencana di Indonesia',
                'category' => 'verifikasi',
                'topic' => 'saluran-verifikasi',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan data resmi lembaga pemerintah)',
                'source_url' => 'sintetis://cekarah-team/saluran-verifikasi-resmi',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Daftar saluran resmi yang bisa digunakan untuk memverifikasi informasi bencana di Indonesia:

SUMBER INFORMASI CUACA DAN GEMPA:
- bmkg.go.id — data gempa real-time, prakiraan cuaca, peringatan dini tsunami
- Aplikasi Info BMKG (Android/iOS) — notifikasi gempa dan cuaca otomatis
- Twitter/X: @InfoBMKG — cepat dan real-time
- Instagram: @infobmkg

SUMBER INFORMASI BENCANA DAN PENANGANAN:
- bnpb.go.id — peta bencana aktif, laporan situasi, pengumuman resmi
- Twitter/X: @BNPB_Indonesia
- Instagram: @bnpb_indonesia
- geoportal.bnpb.go.id — peta bencana interaktif

VERIFIKASI HOAKS DAN ADUAN:
- turnbackhoax.id — database hoaks yang sudah diverifikasi
- cekfakta.com — kolaborasi media untuk cek fakta
- aduankonten.id — platform aduan konten hoaks ke Kemkomdigi
- kominfo.go.id — informasi resmi Kementerian Komunikasi

MEDIA PENYIARAN RESMI (wajib siarkan peringatan bencana):
- TVRI Nasional — sumber terpercaya untuk siaran darurat
- RRI (Radio Republik Indonesia) — jangkauan luas, berguna saat listrik mati
- Semua TV nasional diwajibkan menyiarkan emergency broadcast

MEDIA BERITA TERVERIFIKASI:
Media yang terdaftar di Dewan Pers Indonesia bisa dipercaya untuk berita bencana. Cek di dewanpers.or.id apakah media tersebut terdaftar.

Prinsip: jika informasi tidak ada di sumber resmi di atas, jangan sebarkan terlebih dahulu — verifikasi dulu.
TEXT,
            ],

            [
                'title' => 'Cara Melaporkan Konten Hoaks Bencana ke aduankonten.id (Kemkomdigi)',
                'category' => 'verifikasi',
                'topic' => 'lapor-hoaks',
                'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan panduan Kemkomdigi)',
                'source_url' => 'sintetis://cekarah-team/cara-lapor-hoaks-aduankonten',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
Cara melaporkan konten hoaks bencana ke platform resmi pemerintah:

PLATFORM RESMI PELAPORAN:
1. aduankonten.id (website resmi Kementerian Komunikasi dan Informatika)
2. Email: aduankonten@kominfo.go.id
3. Twitter/X: @aduankonten

JENIS KONTEN YANG BISA DILAPORKAN:
- Berita bencana palsu atau dimanipulasi
- Nomor rekening donasi mencurigakan mengatasnamakan bencana
- Video/foto hoaks yang diklaim dari lokasi bencana
- Konten yang menghasut kepanikan atau memberi instruksi tidak resmi
- Akun yang menyebarkan disinformasi bencana

CARA MELAPORKAN DI aduankonten.id:
1. Buka aduankonten.id di browser
2. Klik "Aduan Konten"
3. Isi formulir: URL konten yang dilaporkan, jenis pelanggaran (pilih "Hoaks/Disinformasi")
4. Tulis deskripsi singkat mengapa konten ini dianggap hoaks
5. Sertakan bukti pembanding: link ke sumber resmi yang membantah
6. Submit — kamu akan mendapat nomor tiket pelaporan

LAPORAN MELALUI PLATFORM MEDIA SOSIAL:
- WhatsApp: laporkan ke nomor resmi WhatsApp jika konten menyebar via grup
- Facebook/Instagram: gunakan fitur "Report" → "False Information"
- Twitter/X: gunakan fitur "Report Tweet" → "It's misleading"
- YouTube: "Report" → "Misleading or dangerous acts"

TIPS PELAPORAN YANG EFEKTIF:
- Sertakan URL langsung ke konten, bukan screenshot
- Berikan konteks: jelaskan mengapa ini hoaks, bukan hanya bahwa ini hoaks
- Laporkan ke lebih dari satu platform sekaligus untuk percepatan penanganan

Waktu respons Kemkomdigi biasanya 1-3 hari kerja untuk konten dengan dampak tinggi.
TEXT,
            ],

            [
                'title' => 'Studi Kasus: Hoaks "Air Laut Naik" Pidie Jaya Aceh, Desember 2025',
                'category' => 'verifikasi',
                'topic' => 'studi-kasus-hoaks',
                'source_name' => 'Data Sintetis Tim Cekarah (studi kasus sintetis berdasarkan pola hoaks nyata)',
                'source_url' => 'sintetis://cekarah-team/studi-kasus-hoaks-pidie-jaya-2025',
                'source_date' => '2026-01-01',
                'content' => <<<'TEXT'
STUDI KASUS SINTETIS: Hoaks Tsunami Pidie Jaya, Aceh — Desember 2025

KRONOLOGI:
Tanggal 12 Desember 2025 malam, gempa M 5,8 mengguncang wilayah Pidie Jaya, Aceh. Tidak ada potensi tsunami menurut BMKG.

Pukul 21.43 WIB: Pesan WhatsApp pertama beredar — "AIR LAUT NAIK DI PIDIE JAYA! Lari ke bukit! Info dari saudara yang di sana. SEBARKAN!!!"

Pukul 22.00-23.30 WIB: Pesan menyebar ke ratusan grup WhatsApp. Warga di wilayah pantai Pidie Jaya berlarian meninggalkan rumah. Kemacetan parah di jalur evakuasi. Dua warga lanjut usia jatuh dan mengalami cedera serius saat berlari.

Pukul 22.15 WIB: BMKG menegaskan via Twitter: "TIDAK ADA POTENSI TSUNAMI dari gempa M 5,8 Pidie Jaya. Masyarakat harap tenang dan tidak terpengaruh informasi yang belum terverifikasi."

Pukul 23.45 WIB: BNPB mengeluarkan pernyataan resmi yang mengklarifikasi hoaks tersebut. Situasi mulai kondusif.

DAMPAK HOAKS:
- 2 warga lansia cedera akibat panik saat berlari
- Operasi SAR terganggu karena jalan tersumbat warga yang mengungsi
- Kerugian ekonomi: toko-toko tutup mendadak, barang dagangan ditinggal

ANALISIS: Mengapa Hoaks Ini Berhasil Menyebar?
1. Terjadi malam hari — warga memang sudah cemas karena gempa
2. Kalimat menggunakan "saudara yang di sana" — terasa personal dan terpercaya
3. Tidak ada yang meluangkan waktu 30 detik untuk cek bmkg.go.id

PELAJARAN:
Sebelum meneruskan pesan bencana, SELALU cek bmkg.go.id terlebih dahulu. 30 detik verifikasi bisa mencegah kepanikan massal yang merenggut nyawa.
TEXT,
            ],
        ];

        foreach ($documents as $data) {
            KnowledgeDocument::create(array_merge($data, ['is_active' => true]));
        }

        $this->command->info('Seeded '.count($documents).' knowledge documents.');
    }
}
