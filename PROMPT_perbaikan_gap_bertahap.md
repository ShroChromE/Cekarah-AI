# Prompt untuk Claude Code — Perbaikan Gap, Sinkronisasi Proposal, Bahan Tambahan

Salin seluruh isi file ini ke Claude Code di root project Cekarah. **Kerjakan satu tahap, tampilkan hasilnya, lalu STOP menunggu konfirmasi saya sebelum lanjut ke tahap berikutnya.** Jangan kerjakan dua tahap sekaligus dalam satu respons.

---

## KONTEKS

Audit sebelumnya menemukan 7 gap antara apa yang diklaim di proposal/dokumentasi dan apa yang sebenarnya ada di kode. Tugasmu memperbaiki gap-gap ini secara bertahap, lalu mencocokkan hasil akhirnya dengan draf proposal terbaru (`Proposal_Cekarah.md`, sudah saya lampirkan terpisah), dan terakhir mengumpulkan bahan mentah tambahan untuk proposal — **bukan menulis rekomendasi strategis**, itu akan saya kerjakan sendiri di luar Claude Code setelah laporanmu selesai.

**Aturan kejujuran tetap berlaku** seperti audit sebelumnya: jangan menutupi gap dengan kosmetik dokumentasi kalau seharusnya itu perbaikan kode sungguhan, tapi juga jangan mengklaim sudah "diperbaiki penuh" kalau yang dilakukan cuma mengubah deskripsi/komentar. Sebutkan jenis perbaikan: `[PERBAIKAN KODE]` atau `[PERBAIKAN DOKUMENTASI/KLAIM]` untuk setiap gap.

---

## TAHAP 1 — Pin Versi Model AI (Gap #6)

- Model embedding Gemini belum di-pin versi spesifiknya di kode (hanya disebut "embeddings Gemini" generik).
- Cari di config/`.env.example`/kode tempat model dipanggil (agen utama `gemini-3-flash-preview`, riset web grounded `gemini-2.5-flash`, dan model embedding).
- Tentukan versi embedding model yang sebenarnya dipakai (cek dependency Laravel AI SDK / dokumentasi API call), pin secara eksplisit di config (jangan hardcode string di banyak tempat — taruh di satu config file, referensikan dari sana).
- Update juga tabel disclosure model AI (jika ada file terpisah) agar menyebutkan 3 model ini dengan versi pasti.
- Output: tunjukkan diff/file yang diubah + konfirmasi versi embedding model yang dipakai.

> **STOP. Tunggu konfirmasi saya.**

---

## TAHAP 2 — Status pgvector: Kode vs Klaim (Gap #1)

- Saat ini: embedding disimpan sebagai JSONB, cosine similarity dihitung di PHP — BUKAN pakai ekstensi pgvector PostgreSQL.
- Lakukan dua hal:
  1. **Estimasi effort**: berapa besar usaha untuk benar-benar mengaktifkan ekstensi pgvector dan migrasi kolom JSONB → vector dalam sisa waktu pengembangan? Laporkan estimasi ini (jam/kompleksitas), JANGAN langsung dieksekusi.
  2. **Perbaikan dokumentasi**: ubah setiap tempat (komentar kode, README, file disclosure) yang menyebut "pgvector" seolah sudah aktif, jadi deskripsi yang jujur: "embedding disimpan JSONB + cosine similarity dihitung di aplikasi (PHP); pgvector direncanakan untuk versi produksi" — tandai `[PERBAIKAN DOKUMENTASI/KLAIM]`.
- Setelah menampilkan estimasi effort di poin 1, **tunggu saya yang memutuskan** apakah migrasi pgvector sungguhan dikerjakan sekarang atau ditunda — jangan diputuskan sendiri.

> **STOP. Tunggu konfirmasi saya (termasuk keputusan soal migrasi pgvector).**

---

## TAHAP 3 — Sinkronisasi Nama Tabel & Status Emergency Contacts (Gap #2)

- Nama tabel riil di database: `disaster_events`, `aid_programs`, `claim_verifications`, `shelter_locations`, `sources`, `citations`, `intent_logs`, `knowledge_documents` — BUKAN `disasters`/`social_aids`/`hoax_verifications` seperti yang sempat dipakai di dokumen draf sebelumnya.
- Update SEMUA file dokumentasi/disclosure yang masih menyebut nama tabel lama, ganti ke nama tabel riil ini. `[PERBAIKAN DOKUMENTASI/KLAIM]`
- Kontak darurat (BNPB 117, Basarnas 115, dst.) saat ini hardcoded di kode (bukan tabel database). Ada 2 opsi:
  - (a) Tetap hardcoded, tapi didokumentasikan jujur sebagai hardcoded — `[PERBAIKAN DOKUMENTASI/KLAIM]`, atau
  - (b) Dikonversi jadi tabel `emergency_contacts` sungguhan agar konsisten dengan tabel lain dan lebih mudah diupdate — ini `[PERBAIKAN KODE]`, butuh migration + seeder baru.
- **Jangan putuskan sendiri (a) atau (b)** — tampilkan trade-off singkat keduanya, tunggu saya pilih.

> **STOP. Tunggu konfirmasi saya (termasuk pilihan (a)/(b) untuk emergency contacts).**

---

## TAHAP 4 — Label Jujur: AI Generatif vs Logika Algoritmik (Gap #5)

- Fitur "Radar Tren" dan "ekstraksi wilayah" memakai pendekatan algoritmik (token Jaccard similarity / keyword matching), BUKAN AI generatif — sementara fitur lain (agen Gemini + 4 tool + RAG + riset web grounded) memang AI sungguhan.
- Tambahkan komentar/docblock yang jelas di kode kedua fitur ini menjelaskan bahwa ini logika algoritmik, bukan pemanggilan model AI generatif.
- Buat satu tabel ringkas (taruh di file laporan, bukan cuma komentar kode) yang memisahkan dua kategori ini secara eksplisit:

  | Fitur                            | Jenis                                    | Mekanisme                |
  | -------------------------------- | ---------------------------------------- | ------------------------ |
  | Agen percakapan + 4 tool-calling | AI generatif                             | Gemini reasoning         |
  | RAG retrieval dokumen            | AI (embedding) + algoritmik (similarity) | text-embedding + cosine  |
  | Riset web grounded               | AI generatif                             | Gemini grounded search   |
  | Radar Tren                       | Algoritmik                               | Token Jaccard similarity |
  | Ekstraksi wilayah dari teks      | Algoritmik                               | Keyword matching         |

  (Sesuaikan isi tabel ini dengan temuan aktual di kode — contoh di atas hanya kerangka.)

> **STOP. Tunggu konfirmasi saya.**

---

## TAHAP 5 — Perbaikan Kode Responsible AI yang Masih Kosong (Gap #4)

Tiga sub-item, masing-masing **perbaikan kode sungguhan**, bukan sekadar dokumentasi:

1. **Guard privasi NIK** — saat ini `[BELUM ADA DI KODE]`. Tambahkan validasi/filter pada input pengguna: jika terdeteksi pola yang menyerupai NIK (16 digit angka berurutan, atau pola umum NIK Indonesia), sistem TIDAK memproses angka tersebut sebagai data untuk disimpan/diteruskan ke model, dan merespons dengan arahan ke kanal resmi (cekbansos.kemensos.go.id) tanpa pernah menyimpan angka tersebut di log/database.
2. **Disclaimer medis/hukum** — saat ini `[BELUM ADA DI KODE]`. Tambahkan ke system prompt/instruction Gemini: larangan eksplisit memberi diagnosis medis atau nasihat hukum definitif, dan template respons yang mengarahkan ke tenaga profesional/instansi resmi untuk pertanyaan semacam itu.
3. **Eskalasi darurat — backstop deterministik** — saat ini `[SEBAGIAN]`, hanya mengandalkan kepatuhan model. Tambahkan deteksi keyword sederhana (mis. "terjebak", "tidak bisa keluar", "sekarat", "tenggelam", dsb. — sesuaikan dengan konteks bencana) sebagai lapisan tambahan SELAIN reasoning model, supaya saat model gagal mendeteksi urgensi, sistem tetap menampilkan kontak darurat (117/115) secara otomatis.

Untuk ketiga sub-item, tampilkan kode yang ditambahkan + cara mengujinya (contoh input yang harus memicu masing-masing guard).

> **STOP. Tunggu konfirmasi saya.**

---

## TAHAP 6 — Mengatasi Rasio Data Sintetis di `knowledge_documents` (Gap #3)

- Saat ini 20 dari 30 baris `knowledge_documents` masih `sintetis://` (tidak tertelusur ke sumber resmi nyata), hanya 10 + tabel terstruktur lain yang bersumber resmi.
- Langkah:
  1. Tampilkan daftar lengkap 20 entri sintetis tersebut (judul/isi ringkas) supaya saya bisa lihat mana yang sebenarnya bisa diganti dengan data resmi yang sudah saya kumpulkan sebelumnya (file `kb_01`–`kb_04` dan dataset BNPB xlsx yang sudah di-seed ke `disaster_events`/`shelter_locations`/`aid_programs`/`claim_verifications`).
  2. Untuk entri yang TIDAK ada penggantinya dari data resmi yang sudah ada, JANGAN dihapus dan JANGAN dipaksa diganti dengan karangan baru — biarkan tetap `is_simulated = true`, tapi pastikan ini konsisten ditandai di proposal sebagai data ilustrasi.
  3. Laporkan rasio akhir setelah langkah ini: berapa entri resmi vs simulasi.
- **Jangan mengeksekusi penggantian data sintetis dengan data baru sebelum saya konfirmasi mana yang boleh diganti** — ini menyangkut akurasi konten yang dipresentasikan ke juri, saya perlu cek dulu.

> **STOP. Tunggu konfirmasi saya (termasuk daftar mana yang boleh diganti).**

---

## TAHAP 7 — Re-seed Data Demo untuk Fitur Radar (Gap #7)

- `intent_logs` saat ini hanya 8 baris dan 0 simulasi — tidak cukup untuk demo fitur Radar Tren terlihat bekerja secara meyakinkan di hadapan juri.
- Jalankan/perbaiki `RadarSimulationSeeder` (atau buat jika belum ada) untuk menghasilkan volume data intent_logs yang representatif untuk demo.
- **Tandai dengan jelas di seeder/komentar bahwa data ini adalah data simulasi untuk keperluan demo**, BUKAN log percakapan asli pengguna — supaya saat ditanya juri, tim tidak salah menyebut ini sebagai data pengguna riil.
- Setelah seeding, jalankan query count untuk verifikasi jumlah baris final di SEMUA tabel berikut (bukan cuma intent_logs): `disaster_events`, `shelter_locations`, `aid_programs`, `claim_verifications`, `sources`, `knowledge_documents`, `intent_logs`. Laporkan angka final masing-masing.

> **STOP. Tunggu konfirmasi saya.**

---

## TAHAP 8 — Cek Ulang Kesesuaian dengan Proposal Terbaru

- Sekarang bandingkan SELURUH hasil Tahap 1–7 dengan isi `Proposal_Cekarah.md` yang sudah dilampirkan.
- Untuk SETIAP klaim teknis di proposal tersebut (nama model, nama tabel, arsitektur RAG, jumlah data, status pgvector, dst.), cek apakah sudah cocok dengan kondisi kode setelah perbaikan Tahap 1–7.
- Buat tabel laporan:

  | Klaim di Proposal | Sesuai Kode Setelah Perbaikan? | Catatan |
  | ----------------- | ------------------------------ | ------- |
  | ...               | Ya / Tidak / Sebagian          | ...     |

- Jika masih ada ketidaksesuaian, JANGAN langsung mengedit proposal — cukup laporkan, biar saya yang putuskan mau ubah di proposal atau di kode.

> **STOP. Tunggu konfirmasi saya.**

---

## TAHAP 9 — Kumpulkan Bahan Mentah Tambahan (BUKAN rekomendasi strategis)

Ini tahap terakhir, dan **scope-nya dibatasi**: kamu HANYA mengumpulkan fakta-fakta dari kode yang berpotensi relevan untuk proposal tapi BELUM disebutkan di `Proposal_Cekarah.md`. **Jangan menulis rekomendasi strategis, jangan menulis ulang bagian proposal** — itu akan saya kerjakan sendiri di luar Claude Code, karena perlu dipetakan ke rubrik penilaian LKS secara strategis.

Cukup buat daftar poin seperti ini:

```
- [Fakta dari kode] — [di mana ditemukan] — [proposal belum menyebutkan ini]
```

Contoh kategori yang perlu disisir (sesuaikan dengan temuan riil, bukan contoh ini secara harfiah):

- Fitur/tool yang ada di kode tapi tidak disebut sama sekali di proposal.
- Detail implementasi Responsible AI hasil Tahap 5 yang membuktikan klaim proposal (sekarang ada kodenya, bukan cuma janji).
- Detail soal data riil (jumlah baris final, sumber resmi yang dipakai) yang lebih kaya dari yang disebutkan proposal saat ini.
- Keterbatasan teknis yang sebaiknya disebutkan sebagai caveat jujur di proposal (mis. status pgvector, rasio data sintetis final).

Simpan seluruh hasil Tahap 1–9 dalam satu file `LAPORAN_PERBAIKAN_GAP.md`.

---

## SETELAH TAHAP 9 SELESAI

Sesuaikan kembali isi kode yang dijalanakan dengan command php artisan cekarah:evaluate untuk kondisi setelah pembaruan, setelah itu jalanakan dan data data nya dikirim dan dimasukan ke LAPORAN \_PERBAIKAN_GAP.md sebagai bahan pertimbangan untuk dimasukan ke proposal nanti.

Bawa file `LAPORAN_PERBAIKAN_GAP.md` ke sesi chat ini (bukan ke Claude Code) — saya akan memetakan bahan mentah dari Tahap 9 itu ke rekomendasi konkret yang sesuai rubrik dan ketentuan teknis Panduan Ekshibisi KA LKS Dikmen 2026.
