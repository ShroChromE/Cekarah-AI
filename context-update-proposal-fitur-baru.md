# Konteks Pembaruan Proposal — Fitur Baru Cekarah (Selaras Rubrik LKS Dikmen 2026)

> Dokumen ini untuk diberikan ke **Claude (chat)** guna menyesuaikan ulang proposal solusi / Problem Canvas Cekarah agar selaras dengan **Panduan Ekshibisi Kompetisi Kecerdasan Artifisial (KA) LKS Dikmen Tingkat Nasional 2026**. Fokus: substansi & implikasi terhadap penilaian — bukan log teknis. Tiga fitur baru yang dibahas: **(1) Portal Relawan (human-in-the-loop)**, **(2) Skrip Evaluasi Otomatis**, **(3) Radar Tren Hoaks & Kebutuhan** — ditambah kapabilitas pendukung **pencarian web real-time**.

---

## Ringkasan Tiga Fitur Baru (untuk konteks penulis ulang proposal)

1. **Portal Relawan** — dashboard ber-login terpisah (peran `admin`/`volunteer`; warga tetap tanpa login). Relawan dapat menambah/mengubah data **posko, bantuan, dan klaim cek-fakta** yang **dipakai langsung** oleh chatbot publik. Ada **antrean "Perlu Ditinjau"** (otomatis terisi ketika sistem tak menemukan data resmi), **sinkronisasi RAG otomatis** (klaim baru langsung di-embedding & tersurfaced di chat tanpa redeploy), dan **jejak audit** (siapa membuat/mengubah, kapan).
2. **Skrip Evaluasi Otomatis** (`php artisan cekarah:evaluate`) — menguji 40 pertanyaan ground-truth (5 kategori) dan menghasilkan **angka aktual**: akurasi klasifikasi intent, % jawaban bersitasi, % keberhasilan menolak pertanyaan di luar konteks, akurasi status verifikasi klaim, dan latency — tanpa menulis ke data produksi.
3. **Radar Tren Hoaks & Kebutuhan** — modul agregasi di Portal Relawan yang mengelompokkan log percakapan menjadi (a) **klaim hoaks sejenis yang sedang naik** dan (b) **wilayah dengan lonjakan kebutuhan**, dengan label jujur "perlu perhatian" (sinyal, bukan kepastian statistik).
4. **Pencarian web real-time** — keempat tool kini juga menarik data terkini dari internet (Google Search grounding) di samping basis data internal, dengan tetap mengutamakan sumber resmi.

---

## 1. Bagian "Target Pengguna"

Proposal lama kemungkinan mencantumkan segmen **"Relawan & organisasi kemanusiaan (PMI, MDMC, Tagana)"** di tabel target pengguna, namun belum ada produk konkret yang melayaninya. **Portal Relawan kini benar-benar mengisi segmen itu**:

- Relawan/organisasi punya antarmuka nyata untuk **mengkurasi data lapangan** (posko, bantuan, klaim) yang langsung memperbaiki jawaban ke warga.
- Memunculkan **dua sisi pengguna yang saling menguatkan**: warga (konsumen informasi, tanpa login) dan relawan (produsen/validator informasi, ber-login) — memperjelas narasi bahwa Cekarah adalah *platform*, bukan sekadar chatbot satu arah.

**Saran revisi:** ubah deskripsi segmen relawan dari "potensial/akan dilayani" menjadi "dilayani via Portal Relawan" dengan menyebut kapabilitas konkret (kurasi data, antrean tinjauan, jejak audit).

---

## 2. Bagian "Responsible AI"

Ini bagian yang **paling diperkuat** oleh fitur baru, langsung menjawab syarat panduan tentang **"di mana penilaian manusia tetap berperan dalam sistem"** — kini bukan lagi klaim naratif, melainkan **bukti konkret**:

- **Human-in-the-loop yang terbukti:** antrean "Perlu Ditinjau" → relawan menambah data resmi → jawaban chatbot berubah dengan sumber baru. Alur ini dapat didemokan end-to-end (sudah diverifikasi: menambah klaim "Bendungan Namo Rambe akan jebol" via portal membuat chat publik menjawabnya sebagai hoaks dengan rujukan relawan).
- **Jejak audit (created_by/updated_by):** setiap perubahan data tercatat siapa & kapan — akuntabilitas yang bisa ditunjukkan ke juri.
- **Radar Tren dengan framing jujur:** ditampilkan sebagai "sinyal yang perlu ditindaklanjuti manusia", **bukan** "tren hoaks terkonfirmasi". Data ditandai sebagai trafik sistem sendiri (+ penanda `is_simulated` untuk data demo) — selaras prinsip kejujuran data di Caveats.
- **Anti-halusinasi tetap dijaga:** saat data internal & web sama-sama kosong, sistem berkata "belum ada data resmi" dan justru memicu antrean tinjauan relawan.

**Saran revisi:** tambahkan sub-poin "Peran penilaian manusia" yang merujuk Portal Relawan (review queue + audit trail) sebagai mekanisme konkret, dan sub-poin transparansi untuk Radar (sinyal, bukan vonis).

---

## 3. Bagian "Rencana Sumber Data" & "Caveats"

**Perubahan sumber data:**
- **Data hasil kurasi relawan** (`claim_verifications`) kini menjadi bagian knowledge base RAG: setiap klaim baru di-embedding dan dapat ditemukan tool `verify_claim` via kemiripan semantik. Artinya sumber data Cekarah **tumbuh secara partisipatif**, bukan statis dari seeder.
- **Pencarian web real-time** menambah lapisan data terkini dari sumber resmi (BNPB/BMKG/dll) dan media — memperkuat klaim "informasi mutakhir", bukan hanya snapshot.

**Tambahan untuk Caveats (jujur):**
- **Data kurasi bergantung kualitas input relawan** — perlu pelatihan singkat & kewajiban mengisi sumber (sudah dipaksa wajib di form klaim).
- **Data Radar Tren sebagian simulasi** (ditandai `is_simulated`) untuk demo; bukan ukuran penyebaran hoaks riil di masyarakat.
- **Pencarian web membawa risiko sumber tak resmi/hoaks** — dimitigasi dengan instruksi mengutamakan sumber resmi + selalu menyebut sumber, namun tetap perlu kehati-hatian.
- **Pengelompokan Radar memakai kemiripan kata kunci (bukan pgvector)** — cukup untuk sinyal demo; presisi penuh butuh embedding.

---

## 4. Bagian "Rekomendasi & Langkah Pengembangan Bertahap" — Ganti Klaim dengan Angka Aktual

Proposal lama kemungkinan menulis target seperti *"100% jawaban menyertakan sumber"* sebagai klaim. Kini **Skrip Evaluasi Otomatis menghasilkan angka aktual** (contoh hasil run sampel; jalankan ulang untuk angka final):

| Metrik (tolok ukur) | Hasil aktual (sampel) | Status |
|---|---|---|
| Akurasi klasifikasi intent | **100%** | Memenuhi target |
| Jawaban menyertakan sumber | **100%** | Memenuhi target |
| Keberhasilan menolak pertanyaan di luar konteks | **100%** | Memenuhi target |
| Akurasi status verifikasi klaim | **~75%** (deteksi heuristik) | Perlu perbaikan lanjutan |
| Latency rata-rata | ~16–21 dtk/pertanyaan (dengan riset web) | Trade-off real-time |

**Saran revisi:** ganti kalimat target/klaim dengan tabel angka aktual di atas, dan **akui jujur** bahwa akurasi status verifikasi (~75%) masih perlu ditingkatkan — sebagian karena pengukurannya berbasis heuristik kata kunci, bukan kelemahan klasifikasi intent (yang 100%). Ini memperlihatkan kematangan evaluatif, bukan klaim kosong.

---

## 5. Pemetaan ke Kriteria Penilaian Resmi

Argumen ringkas siap pakai untuk proposal & sesi tanya jawab, per kriteria:

| Kriteria (bobot) | Fitur yang paling memperkuat | Argumen kunci |
|---|---|---|
| **Pemahaman Masalah (20%)** | Radar Tren | Cekarah tak hanya menjawab satu-satu, tapi **mendeteksi pola kolektif** (hoaks/kebutuhan yang naik) — bukti pemahaman bahwa krisis bersifat sistemik, bukan individual. |
| **Kreativitas & Inovasi (20%)** | Portal Relawan + Radar | Kombinasi **human-in-the-loop partisipatif** + **deteksi dini berbasis log** menjadikan Cekarah platform 2-arah, bukan chatbot biasa — diferensiasi yang jelas. |
| **Pemanfaatan AI (20%)** | Tool-calling + RAG kurasi + web grounding + evaluasi | AI dipakai untuk routing intent, retrieval semantik (termasuk data kurasi relawan), grounding ke web real-time, **dan diukur secara terprogram**. |
| **Responsible AI (15%)** | Portal Relawan + Radar framing | **Bukti konkret peran manusia** (review queue + audit trail) + transparansi data (sinyal, bukan vonis; penanda data simulasi). Menjawab langsung syarat panduan. |
| **Fungsionalitas (15%)** | Skrip Evaluasi | Fungsionalitas **dibuktikan dengan angka** (intent 100%, sitasi 100%, penolakan 100%), bukan klaim — kredibilitas teknis. |
| **Presentasi (10%)** | Semua (untuk video/demo) | Alur human-in-the-loop & dashboard Radar adalah **materi visual kuat** untuk pitch (lihat dokumen video terpisah). |

---

## Catatan Penutup

Saat menulis ulang proposal, pertahankan struktur & bahasa yang dipakai panduan resmi LKS agar terasa "menjawab rubrik". Gunakan angka aktual dari Skrip Evaluasi (jalankan `php artisan cekarah:evaluate` untuk versi final sebelum submission), dan tonjolkan bukti human-in-the-loop sebagai inti narasi Responsible AI.
