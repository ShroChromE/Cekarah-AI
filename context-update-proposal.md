# Konteks Perubahan untuk Revisi Proposal — Cekarah

> Dokumen ini merangkum perubahan dari **rencana awal proposal** ke **implementasi aktual** Cekarah, disusun mengikuti struktur proposal (Problem Canvas). Tujuannya membantu menyesuaikan ulang narasi proposal agar selaras dengan apa yang benar-benar sudah berfungsi. Fokus pada substansi & implikasi, bukan detail kode.

---

## 1. Latar Belakang

**Tetap relevan.** Premis dasar tidak berubah: dalam 48 jam pertama krisis, warga menghadapi dua masalah — (a) tidak tahu ke mana mencari bantuan resmi, dan (b) sulit membedakan informasi benar vs hoaks. Data pendukung (korban bencana Sumatera Nov 2025, ribuan konten hoaks teridentifikasi) tetap dipakai sebagai justifikasi.

**Penajaman:** ruang lingkup kini lebih konkret. Dari dua kapabilitas umum ("navigasi bantuan" & "verifikasi"), implementasi memecahnya menjadi **5 kategori intent** yang lebih spesifik dan dapat didemokan satu per satu (lihat bagian 3).

---

## 2. Target Pengguna

**Tetap relevan:** warga terdampak bencana + keluarga yang mencari informasi. Tidak ada perubahan segmen.

**Tambahan untuk proposal:** karena sistem kini menolak pertanyaan di luar konteks bencana secara eksplisit, posisi produk lebih tegas sebagai **alat khusus krisis**, bukan asisten umum. Ini memperkuat narasi fokus & tepat guna.

---

## 3. Ide Solusi Berbasis AI

**Perubahan paling signifikan dari rencana awal.** Solusi kini berbentuk **routing intent otomatis ke 5 kategori**, di mana model AI sendiri yang memutuskan kapabilitas mana yang dipakai (bukan menu/pilihan manual user, bukan pula if-else kata kunci):

| Kategori | Yang terjadi saat user bertanya |
|----------|---------------------------------|
| 1. Informasi bencana | Sistem mencari kejadian bencana terkini + sumber resmi |
| 2. Verifikasi klaim | Sistem mengecek klaim/hoaks → status + penjelasan + rujukan |
| 3. Lokasi posko | Sistem menampilkan posko/shelter + **peta interaktif** |
| 4. Bantuan sosial | Sistem menjelaskan program bantuan yang tersedia di wilayah |
| 5. Di luar konteks | Sistem menolak sopan & mengarahkan kembali ke topik bencana |

**Tambahan dari rencana awal:** respons kini bersifat **streaming** (teks muncul bertahap seperti ChatGPT/Claude), sehingga user tidak menunggu layar kosong. Ini meningkatkan kesan responsif dan kepercayaan.

**Implikasi narasi:** klaim "AI memandu warga menemukan bantuan & memverifikasi informasi" sekarang dapat dibuktikan dengan alur konkret + tool spesifik, bukan klaim abstrak.

---

## 4. Pendekatan Teknis & Arsitektur AI

**Memperkuat klaim grounding/RAG di proposal.** Arsitektur final:

- **Tool-calling (function-calling) Gemini** sebagai mekanisme routing. Empat tool: `search_disaster_info`, `verify_claim`, `find_shelter_locations`, `get_aid_assistance_info`. Model memilih tool berdasarkan deskripsi tool (reasoning), bukan klasifikasi manual.
- **Grounding ke basis data**: setiap tool query ke tabel terstruktur (Eloquent). Jawaban disusun dari hasil tool, bukan dari pengetahuan umum model. Jika tool tidak menemukan data → sistem menjawab **"belum ada data resmi"**, bukan mengarang.
- **RAG semantik** (untuk info bencana & verifikasi klaim) memakai embedding `text-embedding-004` + pencarian kemiripan. **Catatan teknis penting:** pgvector tidak tersedia di lingkungan pengembangan, sehingga embedding disimpan sebagai kolom JSONB dan kemiripan kosinus dihitung di sisi aplikasi (PHP). Secara fungsional identik untuk skala demo; untuk skala produksi, pgvector tetap menjadi rekomendasi.
- **Skema database** (penamaan Inggris, RESTful): `disaster_events` (hub) yang dihubungkan ke `shelter_locations`, `aid_programs`, dan `claim_verifications`; tabel `sources` + `citations` (relasi polimorfik) untuk rujukan yang reusable di semua kategori; `intent_logs` untuk mencatat kategori intent tiap pesan.
- **Model AI**: `gemini-3-flash-preview` (teks) + `text-embedding-004` (embedding), via Laravel AI SDK native.

**Implikasi narasi:** jika proposal lama menyebut "RAG sederhana", kini bisa dipertajam menjadi "agen AI dengan tool-calling + retrieval terstruktur & semantik, dengan logging intent untuk auditabilitas".

---

## 5. Rencana Sumber Data

**Sudah terimplementasi, bukan lagi rencana.** Dataset bersifat **sintetis namun dibangun dari pola sumber resmi yang valid**, terhubung secara logis ke satu peristiwa bencana (banjir hidrometeorologi Sumatera Utara 2025, fokus Binjai):

- Lembaga yang dirujuk: **BNPB, BMKG, BPBD (Sumut & Binjai), Kemensos/Cek Bansos, PMI, MAFINDO/TurnBackHoax, Kemkomdigi**.
- Setiap entri data punya rujukan (nama sumber, URL, tanggal) yang ditampilkan sebagai **link yang bisa diklik** di UI.
- Setiap baris ditandai `is_simulated = true` sebagai penanda transparansi etik (data ilustrasi, bukan klaim angka resmi pasti).
- Cakupan: 4 peristiwa banjir, 3 lokasi posko di Binjai (dengan koordinat), 2 program bantuan, 3 entri verifikasi klaim termasuk pola hoaks nyata ("air laut naik" Pidie Jaya, Aceh).

**Implikasi narasi:** memperkuat bagian "rencana sumber data" menjadi "dataset sintetis-bersumber-valid dengan sitasi yang dapat ditelusuri" — penting untuk pertanyaan juri soal asal data.

---

## 6. Responsible AI

**Bagian ini paling diperkuat oleh implementasi.** Poin yang sebaiknya **ditambahkan/ditegaskan** di proposal:

- **Anti-halusinasi (grounding):** model dipaksa menjawab dari hasil tool/DB; bila kosong → "belum ada data resmi" + arahkan ke sumber resmi. Tidak mengarang angka.
- **Transparansi sumber:** setiap jawaban kategori 1–4 menampilkan rujukan (nama + tanggal + tautan resmi). Data sintetis ditandai eksplisit (`is_simulated`).
- **Tidak ada vonis biner:** verifikasi klaim tidak memvonis "HOAKS/FAKTA" mentah, melainkan menjelaskan dengan alasan + rujukan (mis. hanya BMKG yang berwenang soal peringatan dini).
- **Eskalasi ke manusia:** kontak petugas resmi (BNPB 117 ext 7, Basarnas 115) tersedia; panel eskalasi muncul saat keyakinan rendah atau situasi darurat.
- **Transparansi keyakinan:** tiap jawaban menampilkan tingkat keyakinan; bila < 60%, sistem mendorong verifikasi ke sumber resmi.
- **Penolakan di luar lingkup:** sistem tidak menjawab pertanyaan non-bencana — mengurangi risiko penyalahgunaan & halusinasi lintas-domain.
- **Privasi:** tidak ada data pribadi; sesi diidentifikasi token acak; data sintetis bukan data korban asli.
- **Auditabilitas:** `intent_logs` mencatat kategori & tool tiap pesan untuk analitik & evaluasi.

---

## 7. Roadmap / Tahapan Pengembangan

**Beberapa tahap yang direncanakan kini SUDAH tercapai** — bagian "rekomendasi & langkah pengembangan bertahap" perlu diperbarui agar tidak menyebut hal yang sudah selesai sebagai rencana masa depan:

| Tahap | Status |
|-------|--------|
| Streaming response | ✅ Selesai |
| Skema DB 5 kategori | ✅ Selesai |
| Routing intent + tool-calling | ✅ Selesai |
| Dataset sintetis bersumber valid | ✅ Selesai |
| UI peta posko + sumber yang bisa diklik | ✅ Selesai |

**Pengembangan lanjutan yang masih terbuka** (untuk roadmap ke depan): migrasi ke pgvector pada produksi; integrasi data real-time dari API resmi (BMKG/BNPB); perluasan cakupan wilayah; penanganan pertanyaan ambigu lintas-kategori; moderasi/feedback loop untuk koreksi jawaban.

---

## 8. Caveats (Keterbatasan)

Poin yang **sebaiknya ditambahkan** ke bagian caveats proposal, berdasarkan keterbatasan nyata saat implementasi:

1. **Risiko misklasifikasi intent:** pertanyaan ambigu atau campuran (mis. info + lokasi dalam satu kalimat) bisa membuat model memilih tool yang kurang tepat. Mitigasi: deskripsi tool yang jelas + model memilih satu kebutuhan utama; tetap ada ruang kesalahan.
2. **Sifat data sintetis:** dataset bersifat ilustratif (ditandai `is_simulated`), dibangun dari pola sumber resmi — bukan data operasional real-time. Angka & lokasi adalah simulasi yang realistis, bukan laporan resmi terverifikasi saat ini.
3. **Ketergantungan pada Gemini tool-calling:** kualitas routing bergantung pada kemampuan reasoning model. Pada layanan gratis, ketersediaan model dapat terganggu (mis. respons "sistem sibuk"/503); sudah ditangani dengan retry & pesan fallback berisi kontak darurat.
4. **pgvector belum aktif di lingkungan dev:** kemiripan semantik dihitung di aplikasi (cosine PHP). Memadai untuk skala demo; perlu pgvector untuk skala besar.
5. **Peta butuh API key:** peta posko interaktif memerlukan Google Maps API key; tanpa itu, sistem menampilkan fallback daftar lokasi + tautan ke Google Maps.
