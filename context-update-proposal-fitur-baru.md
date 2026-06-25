# Konteks Update Proposal — Tiga Fitur Baru Cekarah (Selaras Panduan KA LKS Dikmen Nasional 2026)

> **Untuk siapa:** dokumen ini akan diberikan ke **Claude (chat, bukan Claude Code)** untuk membantu menyesuaikan ulang proposal solusi (Problem Canvas) Cekarah. Berbeda dari [context-update-proposal.md](context-update-proposal.md) (yang merangkum fondasi awal), dokumen ini fokus pada **tiga fitur baru** dan **memetakannya secara eksplisit ke ketentuan & rubrik resmi Panduan Ekshibisi KA LKS Dikmen Tingkat Nasional 2026**.
>
> **Tiga fitur baru yang sudah selesai & teruji:**
> 1. **Portal Relawan** — dashboard human-in-the-loop (login admin/volunteer, CRUD posko/bantuan/klaim, review queue jawaban "belum ada data resmi", audit trail, sinkronisasi langsung ke RAG).
> 2. **Skrip Evaluasi Otomatis** — Artisan command `cekarah:evaluate` yang mengukur metrik aktual (sitasi, akurasi intent, akurasi status verifikasi, penolakan out-of-scope, latency) terhadap ground-truth.
> 3. **Radar Tren Hoaks & Kebutuhan** — modul agregasi log chat: cluster klaim sejenis + lonjakan kebutuhan per wilayah, dengan framing "sinyal untuk ditindaklanjuti", bukan kepastian statistik.

---

## 0. Cara Pakai Dokumen Ini

Setiap bagian di bawah ditulis **mengikuti struktur proposal asli** dan, untuk tiap fitur, menyebut **poin panduan / kriteria rubrik** yang diperkuat. Bagian 6 merangkum pemetaan ke 6 kriteria penilaian resmi agar mudah dijadikan narasi tanya-jawab.

> ⚠️ **Catatan kejujuran data:** angka pada bagian 4 (Rekomendasi & Tolok Ukur) harus diisi dari **laporan evaluasi terbaru** yang dihasilkan `php artisan cekarah:evaluate` (tersimpan di `storage/app/evaluations/eval-<timestamp>.md`). Selama laporan belum dijalankan ulang menjelang final, jangan menuliskan angka karangan — gunakan placeholder `[isi dari laporan eval]`.

---

## 1. Bagian "Target Pengguna" — Portal Relawan mengisi segmen yang sebelumnya kosong

**Kondisi proposal lama:** tabel target pengguna mencantumkan segmen **"Relawan & organisasi kemanusiaan (PMI, MDMC, Tagana)"**, tetapi produk hanya melayani warga (chat publik). Segmen relawan tertulis tetapi **tidak terlayani** secara konkret.

**Yang berubah:** Portal Relawan kini menjadi **antarmuka nyata** untuk segmen tersebut:
- Relawan/organisasi login ke dashboard terpisah dari chat warga.
- Mereka menjadi **kurator data**: menambah/menyunting posko, program bantuan, dan klaim hasil cek fakta manual — yang **langsung memengaruhi jawaban ke warga** (sumber data yang sama, bukan duplikasi).
- Review queue mengubah relawan dari penonton menjadi **penindak**: setiap pertanyaan warga yang gagal dijawab ("belum ada data resmi") muncul sebagai antrean tugas yang bisa langsung mereka isi.

**Narasi untuk proposal:** ubah kalimat "menyasar relawan" (aspirasi) menjadi **"menyediakan Portal Relawan sebagai alat kerja kurasi data bagi relawan/organisasi"** (bukti). Ini menjadikan Problem Canvas memiliki **dua sisi pengguna yang sama-sama terlayani** — warga (konsumen informasi) dan relawan (produsen/kurator informasi).

**Poin panduan yang diperkuat:** *Pemahaman Masalah* (segmen pengguna nyata, bukan daftar) & *Fungsionalitas* (fitur untuk segmen kedua benar-benar berjalan).

---

## 2. Bagian "Responsible AI" — Portal Relawan = bukti konkret peran manusia; Radar Tren menjaga kejujuran framing

Panduan resmi mensyaratkan penjelasan **"di mana penilaian manusia tetap berperan dalam sistem"**. Pada proposal lama ini hanya kalimat naratif ("AI memandu, manusia memutuskan"). Sekarang ada **bukti mekanis**:

**a. Human-in-the-loop yang dapat ditunjukkan (Portal Relawan):**
- **Review queue** = titik di mana sistem secara eksplisit menyerahkan keputusan ke manusia ketika tidak punya data resmi (kolom `needs_review` di-set otomatis oleh tool saat fallback).
- **Kurasi manual → RAG** = penilaian manusia (klaim terverifikasi + sumber wajib) langsung masuk knowledge base dan dipakai menjawab warga. Manusia bukan sekadar "mengawasi", tetapi **menambahkan kebenaran** ke sistem.
- **Audit trail** (`created_by` / `updated_by` + waktu) = bukti **siapa** menilai **apa** dan **kapan** — bisa diperlihatkan ke juri, bukan diklaim.

**b. Kejujuran data (Radar Tren):**
- Insight lonjakan klaim/kebutuhan disajikan sebagai **"Perlu perhatian"**, bukan **"Confirmed trending"** atau "PASTI hoaks masif".
- Dashboard secara eksplisit menyatakan ini **sinyal dari trafik sistem sendiri**, bukan pengukuran resmi penyebaran hoaks di masyarakat.
- Data demo ditandai tegas (`is_simulated`) dan dapat difilter terpisah (Semua / Live / Simulasi).

**Narasi untuk proposal:** pada bagian Responsible AI, ganti klaim normatif dengan **alur konkret**: *"Saat AI tidak yakin/ tidak punya data → masuk review queue → relawan manusia mengisi data bersumber → jawaban warga membaik."* Ini langsung menjawab syarat panduan tentang peran manusia.

**Poin panduan yang diperkuat:** *Responsible AI (15%)* — paling kuat; juga *Pemanfaatan AI* (loop perbaikan berbasis manusia).

---

## 3. Bagian "Rencana Sumber Data" / "Caveats" — implikasi kurasi manual & data simulasi

**Tambahan untuk "Rencana Sumber Data":**
- Knowledge base RAG kini punya **dua asal**: (a) dataset sintetis-bersumber-valid awal, dan (b) **`curated_claims` hasil kurasi relawan** yang di-*embed* otomatis saat disimpan sehingga langsung dapat di-retrieve oleh tool `verify_claim` tanpa re-seed/re-deploy.
- Setiap entri kurasi **wajib menyertakan sumber** (nama, URL, tanggal) — prinsip grounding dipertahankan bahkan untuk data buatan manusia.

**Tambahan untuk "Caveats":**
1. **Kualitas data kurasi bergantung pada relawan.** Karena entri relawan langsung memengaruhi jawaban warga, kontrol kualitas (kewajiban sumber + audit trail) menjadi mitigasi penting; tetap ada risiko human error yang perlu diakui jujur.
2. **Radar Tren memakai data simulasi untuk demo.** Grafik lonjakan saat presentasi sebagian/semuanya berasal dari **seeder simulasi** (ditandai `is_simulated`), bukan trafik pengguna riil. Ini disebut terbuka agar tidak menyesatkan juri.
3. **Radar = sinyal internal, bukan statistik populasi.** Lonjakan mencerminkan pertanyaan yang masuk ke sistem, bukan prevalensi hoaks/kebutuhan sebenarnya di lapangan.
4. **Ekstraksi wilayah bersifat keyword-based** (deterministik, offline) — cepat & murah, tetapi hanya mengenali daftar wilayah yang diketahui; pertanyaan tanpa nama wilayah dikecualikan dari tampilan per-wilayah.

**Poin panduan yang diperkuat:** *Responsible AI* (transparansi keterbatasan) & *Pemahaman Masalah* (sadar batas data).

---

## 4. Bagian "Rekomendasi & Langkah Pengembangan Bertahap" — ganti target dengan ANGKA AKTUAL

Proposal lama menulis tolok ukur sebagai **klaim/target** (mis. "100% jawaban menyertakan sumber"). Skrip Evaluasi Otomatis (Fase 3) menggantinya dengan **pengukuran nyata**. Jalankan `php artisan cekarah:evaluate` lalu isi tabel dari `storage/app/evaluations/eval-<timestamp>.md`:

| Metrik | Target proposal lama | Angka aktual (isi dari laporan eval) |
|---|---|---|
| Jawaban menyertakan sumber + tanggal | "100%" (klaim) | `[isi dari laporan eval]` % |
| Akurasi klasifikasi intent | implisit "akurat" | `[isi dari laporan eval]` % |
| Akurasi status verifikasi klaim | "≥ ambang internal" | `[isi dari laporan eval]` % |
| Keberhasilan menolak out-of-scope | implisit | `[isi dari laporan eval]` % |
| Latency rata-rata per kategori | tidak ada | `[isi dari laporan eval]` ms |

**Narasi untuk proposal:**
- Kalimat yang sudah memenuhi target → sampaikan sebagai **fakta terukur** ("X% jawaban menyertakan sumber, diukur atas N kasus uji").
- Kategori dengan akurasi rendah → **akui jujur** dan jadikan butir roadmap ("kategori Y masih Z%; rencana perbaikan: perkaya dataset/perjelas deskripsi tool"). Kejujuran ini justru menambah kredibilitas di mata juri.
- Tambah satu kalimat metodologi: *"Tolok ukur diukur otomatis & dapat direproduksi via Artisan command terhadap ground-truth 75–100 pertanyaan."*

**Poin panduan yang diperkuat:** *Fungsionalitas* (bukti sistem bekerja) & *Pemanfaatan AI* (evaluasi terukur, bukan klaim).

---

## 5. Tinjauan terhadap Kriteria Penilaian Resmi (per fitur)

Bobot resmi: **Pemahaman Masalah 20% · Kreativitas & Inovasi 20% · Pemanfaatan AI 20% · Responsible AI 15% · Fungsionalitas 15% · Presentasi 10%.**

| Fitur baru | Kriteria yang PALING diperkuat | Argumen ringkas untuk narasi/tanya-jawab |
|---|---|---|
| **Portal Relawan** | Responsible AI (15%), Fungsionalitas (15%), Pemahaman Masalah (20%) | "Human-in-the-loop bukan slogan: review queue + kurasi → RAG + audit trail adalah bukti mekanis peran manusia, sekaligus melayani segmen relawan yang sebelumnya hanya tertulis." |
| **Skrip Evaluasi Otomatis** | Fungsionalitas (15%), Pemanfaatan AI (20%) | "Klaim performa kami terukur & reproducible: satu perintah menghasilkan metrik sitasi, akurasi intent, verifikasi, dan latency atas ground-truth." |
| **Radar Tren** | Kreativitas & Inovasi (20%), Pemanfaatan AI (20%) | "Cekarah bukan sekadar chatbot tanya-jawab, tetapi sistem deteksi dini: mengubah ribuan interaksi menjadi sinyal lonjakan klaim & kebutuhan per wilayah — dengan framing jujur sebagai 'perlu perhatian', bukan kepastian." |

**Catatan presentasi (10%):** ketiga fitur memberi materi visual kuat — demo end-to-end human-in-the-loop, tabel metrik nyata, dan grafik radar. Manfaatkan di video pitch (lihat dokumen video terpisah).

---

## 6. Ringkasan Satu Paragraf (untuk pembuka revisi)

> Sejak proposal awal, Cekarah berevolusi dari chatbot tanya-jawab menjadi **ekosistem tiga sisi**: warga (konsumen informasi), relawan (kurator human-in-the-loop melalui Portal Relawan), dan lapisan insight kolektif (Radar Tren sebagai deteksi dini). Klaim kinerja tidak lagi naratif melainkan **diukur otomatis** lewat skrip evaluasi. Tiga penambahan ini secara langsung memperkuat kriteria Responsible AI, Fungsionalitas, Kreativitas, dan Pemanfaatan AI dalam rubrik resmi — dengan tetap menjaga prinsip kejujuran data (penanda simulasi & framing "perlu perhatian").
