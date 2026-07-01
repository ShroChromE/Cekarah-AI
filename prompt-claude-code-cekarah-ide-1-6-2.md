# Prompt Bertahap untuk Claude Code — Implementasi Ide #1, #6, #2 (Cekarah)

> **Cara pakai:** Dokumen ini terpisah dari file prompt Fase 1-6 sebelumnya (`prompt-claude-code-cekarah.md`), khusus untuk tiga ide tambahan yang dipilih: **Ide #1 (Portal Relawan/Human-in-the-Loop)**, **Ide #6 (Skrip Evaluasi Otomatis)**, dan **Ide #2 (Radar Tren Hoaks & Kebutuhan)**. Copy satu blok "PROMPT UNTUK CLAUDE CODE" per sesi, jangan digabung. Urutan fase di bawah ini sudah disusun sesuai dependensi: Portal Relawan (Fase 1-2) duluan, Skrip Evaluasi (Fase 3) independen/bisa paralel kapan saja, Radar Tren (Fase 4) di akhir karena memanfaatkan struktur dashboard Portal Relawan. Fase 5 dijalankan paling terakhir, setelah Fase 1-4 selesai & teruji.

---

## Konteks Umum (untuk referensi Anda, tidak perlu di-copy)

- Project: **Cekarah** — Laravel 13 + Laravel AI SDK + Gemini, Inertia.js + React, PostgreSQL + pgvector.
- Fondasi yang sudah selesai sebelum dokumen ini (dari file Fase 1-6 terpisah): streaming response chat, skema database 5 kategori intent, tool-calling per kategori intent (4 tool: `search_disaster_info`, `verify_claim`, `find_shelter_locations`, `get_aid_assistance_info`), dataset sintetis bersumber valid, integrasi peta untuk lokasi posko, dan dokumentasi konteks perubahan tahap awal.
- Tiga ide yang akan diimplementasikan di sini:
  1. **Portal Relawan** — dashboard terpisah untuk relawan/organisasi, human-in-the-loop curator atas data shelter/aid/klaim.
  2. **Skrip Evaluasi Otomatis** — pengujian otomatis untuk membuktikan klaim tolok ukur proposal dengan angka nyata.
  3. **Radar Tren Hoaks & Kebutuhan** — agregasi log chat untuk mendeteksi lonjakan klaim/kebutuhan per wilayah, sebagai modul tambahan di Portal Relawan.

---

## FASE 1 — Portal Relawan: Desain Skema Tambahan & Setup Role/Auth

**Tujuan:** Menyiapkan fondasi (skema DB + autentikasi role) untuk Portal Relawan, sebelum membangun fitur CRUD/review queue-nya di Fase 2. Dipisah agar keputusan skema bisa Anda review dulu sebelum dieksekusi.

```
PROMPT UNTUK CLAUDE CODE — FASE 1: SKEMA & AUTH UNTUK PORTAL RELAWAN

Konteks project:
"Cekarah" (Laravel 13 + Laravel AI SDK + Gemini, Inertia.js + React, PostgreSQL + pgvector).
Implementasi sebelumnya (sudah selesai di luar dokumen ini) mencakup: streaming response,
skema database 5 kategori intent, tool-calling per kategori intent (4 tool: search_disaster_info,
verify_claim, find_shelter_locations, get_aid_assistance_info), dataset sintetis bersumber valid,
integrasi peta. Sekarang akan dibangun fitur baru: PORTAL RELAWAN — dashboard terpisah untuk
relawan/organisasi (PMI, MDMC, Tagana, dst.) yang berfungsi sebagai human-in-the-loop curator,
TERPISAH dari chat publik yang dipakai warga (warga tetap akses tanpa login seperti sekarang).

Tujuan Portal Relawan (untuk konteks Anda, bukan untuk dieksekusi semua di fase ini):
- Relawan bisa login, lalu menambah/mengedit data shelter_locations, aid_programs, dan klaim
  hasil cek fakta manual — perubahan ini langsung memengaruhi jawaban tool-calling ke warga
  (data sumber yang sama dipakai, bukan duplikasi).
- Relawan bisa melihat daftar "jawaban yang perlu ditinjau" — yaitu kasus di mana sistem
  menjawab "belum ada data resmi" / fallback karena tidak menemukan dokumen relevan — lalu bisa
  menambahkan data resminya langsung dari sana.

Tugas Anda HANYA untuk fase ini (jangan bangun UI/CRUD-nya dulu, itu scope Fase 2):
1. Audit dulu:
   - Apakah sudah ada sistem user/auth di project ini (Laravel Breeze/Fortify/Sanctum, dsb.)?
     Jika belum ada sama sekali, laporkan opsi yang paling ringan & cocok untuk Inertia.js +
     React sebelum memilih salah satu.
   - Audit ulang tabel `shelter_locations`, `aid_programs`, dan tabel log chat dari implementasi
     sebelumnya (sebutkan nama tabel/kolom final yang ada saat ini) untuk memastikan desain baru
     ini konsisten dan tidak duplikasi.
2. Susun PROPOSAL desain (jangan eksekusi dulu):
   a. Mekanisme role: minimal 2 role — `admin` dan `volunteer` (relawan). Warga TIDAK perlu role
      karena tidak login. Tentukan apakah role disimpan sebagai kolom di tabel `users` atau tabel
      relasi terpisah — jelaskan alasan pilihan Anda.
   b. Tabel baru `curated_claims` (atau nama lain yang lebih konsisten dengan konvensi RESTful/
      English yang sudah dipakai sebelumnya) — untuk menyimpan hasil cek fakta manual oleh
      relawan: teks klaim, status (verified/hoax/unverified), penjelasan, sumber rujukan
      (relasi ke tabel source/citation yang sudah ada), dibuat oleh siapa (relasi ke users),
      dan apakah entri ini sudah "diembed" ke pgvector supaya bisa ikut jadi bagian retrieval RAG
      (kolom embedding atau relasi ke tabel chunk yang sama dengan pola RAG yang sudah ada).
   c. Definisikan secara teknis logika "perlu ditinjau": tambahkan kolom boolean `needs_review`
      pada tabel log chat, di-set TRUE secara otomatis oleh Tool class setiap kali tool tidak
      menemukan dokumen relevan / mengembalikan fallback "belum ada data resmi". Jelaskan di
      proposal Anda, tabel/tool mana saja yang perlu disentuh untuk menambahkan logika ini.
   d. Pertimbangkan kebutuhan audit trail sederhana (siapa mengubah data apa, kapan) pada tabel
      shelter_locations/aid_programs/curated_claims, karena ini relevan untuk narasi Responsible
      AI ("peran penilaian manusia" yang bisa dibuktikan, bukan diklaim).
3. Sajikan proposal ini ke saya dengan format: daftar tabel baru/kolom tambahan, tipe data,
   relasi, serta opsi mekanisme auth — dan TUNGGU saya approve sebelum lanjut membuat migration.
4. Setelah saya approve, buat migration, model Eloquent, dan setup auth dasar (login/logout,
   middleware role) — TANPA membangun halaman CRUD/dashboard-nya (itu scope Fase 2).

Batasan:
- Jangan membangun halaman/UI Portal Relawan di fase ini.
- Jangan mengubah logika tool-calling lebih dari penambahan flag `needs_review` yang sudah
  dijelaskan di atas.
- Jangan mengubah skema yang sudah ada sebelumnya kecuali untuk menambah kolom yang memang
  diperlukan sesuai proposal — jika perlu mengubah kolom yang sudah ada, laporkan dulu sebelum
  eksekusi.
```

---

## FASE 2 — Portal Relawan: CRUD, Review Queue & Sinkronisasi ke RAG

**Tujuan:** Membangun halaman/dashboard nyata untuk relawan, di atas fondasi skema & auth dari Fase 1.

```
PROMPT UNTUK CLAUDE CODE — FASE 2: CRUD & REVIEW QUEUE PORTAL RELAWAN

Konteks project:
"Cekarah" — fondasi skema database & auth/role untuk Portal Relawan sudah selesai di Fase 1
(role admin/volunteer, tabel curated_claims, kolom needs_review di log chat). Sekarang bangun
halaman/dashboard-nya.

Tugas Anda untuk fase ini:
1. Halaman login terpisah untuk relawan/admin (route terpisah dari chat publik warga).
2. Halaman CRUD untuk:
   - `shelter_locations` — tambah/edit/hapus posko, termasuk input latitude/longitude
     (sediakan cara input koordinat yang mudah, mis. klik di peta kecil atau input manual).
   - `aid_programs` — tambah/edit/hapus program bantuan, terhubung ke disaster event & wilayah.
   - `curated_claims` — tambah/edit hasil cek fakta manual, dengan field sumber rujukan yang
     wajib diisi (nama sumber, url, tanggal) sesuai prinsip grounding di proposal kami.
3. PENTING — sinkronisasi ke RAG: setiap kali ada entri baru/update di `curated_claims` (dan
   idealnya juga shelter_locations/aid_programs bila isinya dipakai sebagai konteks jawaban RAG),
   generate embedding-nya menggunakan pipeline embedding yang sudah ada di project (sama seperti
   yang dipakai untuk knowledge base awal sebelumnya), supaya tool-calling langsung bisa mengambil
   data baru ini sebagai konteks jawaban TANPA perlu re-deploy atau re-seed manual. Tunjukkan ke
   saya bagaimana Anda memverifikasi alur ini benar-benar bekerja (mis. tambah data baru via
   portal, lalu tanya hal yang sama via chat publik, dan tunjukkan jawabannya berubah
   mencerminkan data baru tersebut).
4. Halaman "Perlu Ditinjau" (review queue): tampilkan daftar log chat dengan `needs_review = true`
   dari Fase 1, urut dari yang terbaru, beserta pertanyaan asli user dan kategori intent-nya.
   Sediakan aksi cepat: tombol "Tambahkan data resmi" yang langsung membawa relawan ke form
   `curated_claims` atau tabel terkait lainnya (sesuai kategori) dengan pertanyaan asli sudah
   terisi sebagai referensi konteks, supaya alurnya cepat bagi relawan di lapangan.
5. Tambahkan audit trail sederhana yang sudah didesain di Fase 1 (siapa mengubah apa, kapan) —
   tampilkan minimal sebagai riwayat singkat di tiap entri data.
6. Lakukan pengujian end-to-end skenario human-in-the-loop secara lengkap dan laporkan hasilnya
   ke saya: (a) chat publik menjawab "belum ada data resmi" untuk suatu pertanyaan → (b) muncul
   di review queue relawan → (c) relawan menambahkan data resminya → (d) chat publik ditanya hal
   yang sama lagi → (e) jawaban sudah berubah mencerminkan data baru, lengkap dengan sumbernya.

Batasan:
- Jangan membangun fitur Radar Tren (Fase 4) di fase ini.
- Jangan mengubah skema dari Fase 1 kecuali memang ditemukan kebutuhan kolom tambahan saat
  implementasi — laporkan dulu sebelum mengubah.
```

---

## FASE 3 — Skrip Evaluasi Otomatis (Angka Nyata untuk Klaim Proposal)

**Tujuan:** Bangun skrip pengujian otomatis untuk membuktikan klaim tolok ukur di proposal dengan angka aktual. Fase ini independen — bisa dikerjakan paralel kapan saja, tidak bergantung pada Fase 1-2.

```
PROMPT UNTUK CLAUDE CODE — FASE 3: SKRIP EVALUASI OTOMATIS

Konteks project:
"Cekarah" — chatbot dengan 4 tool-calling per kategori intent (info bencana, verifikasi klaim,
lokasi posko, bansos) dan kategori ke-5 (di luar konteks → ditolak). Dataset sintetis bersumber
valid sudah diisi sebelumnya. Proposal kami menulis tolok ukur seperti "100% jawaban menyertakan
sumber + tanggal" dan "akurasi pencocokan klaim ≥ ambang internal" — saat ini itu baru klaim,
belum ada pengukuran aktual.

Tugas Anda untuk fase ini:
1. Audit dulu endpoint/cara memanggil chat secara terprogram (bukan lewat UI manual) — gunakan
   endpoint API yang sama dipakai frontend, atau buat cara panggil langsung ke service/orkestrasi
   AI SDK jika lebih praktis untuk testing otomatis.
2. Bersama saya, susun set pertanyaan uji (ground truth) — minimal 15-20 pertanyaan per kategori
   (total 75-100 pertanyaan), berdasarkan dataset yang sudah ada (gunakan variasi pertanyaan yang
   relevan dengan skenario uji yang sudah kita tetapkan: info banjir, verifikasi klaim termasuk
   kasus "air laut naik" Pidie Jaya, posko Binjai, bansos Binjai, serta pertanyaan out-of-scope
   acak seperti "siapa presiden Indonesia saat ini?"). Untuk tiap pertanyaan, tentukan jawaban
   yang "seharusnya benar" (expected: kategori intent yang benar dipanggil, status verifikasi
   yang benar untuk kategori klaim, ada/tidaknya sitasi, ditolak/tidak untuk out-of-scope).
3. Buat skrip (Laravel Artisan command lebih disarankan supaya mudah dijalankan ulang) yang:
   - Menjalankan seluruh pertanyaan uji secara otomatis ke sistem.
   - Mencatat untuk setiap pertanyaan: tool/kategori yang dipanggil sistem, apakah hasilnya
     menyertakan sumber+tanggal, status verifikasi (untuk kategori klaim), apakah pertanyaan
     out-of-scope berhasil ditolak, dan waktu respons (latency).
   - Membandingkan hasil aktual dengan ground truth, lalu menghitung metrik akhir:
     * % jawaban yang menyertakan sumber+tanggal (per kategori dan total)
     * % akurasi klasifikasi intent (tool yang dipanggil sesuai kategori pertanyaan)
     * % akurasi status verifikasi klaim dibanding ground truth
     * % keberhasilan menolak pertanyaan out-of-scope
     * Latency rata-rata per kategori
   - Mengeluarkan hasil dalam format yang mudah dibaca (tabel ringkasan di terminal dan/atau
     file laporan markdown/JSON) yang bisa langsung saya pakai sebagai bahan proposal & pitch.
4. Jalankan skrip ini, tunjukkan hasilnya ke saya, dan jika ada kategori dengan akurasi rendah,
   laporkan pola kegagalannya (mis. pertanyaan seperti apa yang sering salah diklasifikasi) supaya
   saya bisa putuskan apakah perlu perbaikan ke dataset/tool sebelum deadline.

Batasan:
- Jangan mengubah logika tool-calling/RAG kecuali untuk memperbaiki bug nyata yang ditemukan
  selama pengujian — jika ada, laporkan dulu apa yang ingin diubah dan kenapa, sebelum eksekusi.
- Skrip ini harus aman dijalankan berulang kali tanpa merusak data produksi (gunakan environment
  testing/database terpisah jika memungkinkan, atau pastikan tidak menulis data uji ke tabel
  yang sama dipakai chat publik).
```

---

## FASE 4 — Radar Tren Hoaks & Kebutuhan (Aggregated Insight)

**Tujuan:** Membangun lapisan insight kolektif di atas data log chat, sebagai modul tambahan di Portal Relawan (Fase 1-2). Jalankan setelah Fase 2 selesai, karena memanfaatkan struktur auth & dashboard yang sama.

```
PROMPT UNTUK CLAUDE CODE — FASE 4: RADAR TREN HOAKS & KEBUTUHAN

Konteks project:
"Cekarah" — Portal Relawan (Fase 1-2) sudah selesai dengan auth role admin/volunteer dan halaman
CRUD + review queue. Setiap chat sudah tercatat di log dengan kategori intent, tool yang
dipanggil, dan timestamp. Sekarang akan dibangun modul tambahan di Portal Relawan: RADAR TREN —
agregasi dari log chat untuk mendeteksi pola/lonjakan, bukan cuma menjawab satu-satu.

Tugas Anda untuk fase ini:
1. Audit dulu struktur tabel log chat yang ada (kolom apa saja yang tersedia: kategori intent,
   tool dipanggil, teks pertanyaan asli, wilayah yang disebut jika ada, timestamp) untuk
   menentukan apakah perlu kolom tambahan (mis. ekstraksi nama wilayah dari pertanyaan, jika
   belum ada — laporkan dulu sebelum menambah kolom).
2. Bangun query agregasi untuk dua insight utama:
   a. TREN KLAIM HOAKS — kelompokkan pertanyaan dari kategori "verifikasi klaim" berdasarkan
      kemiripan (gunakan similarity search pgvector yang sudah ada untuk RAG, diterapkan ulang
      untuk clustering klaim yang mirip, bukan identik persis kata-per-kata), lalu hitung jumlah
      kemunculan klaim sejenis per periode waktu (per jam/per hari, sesuaikan dengan kebutuhan
      demo). Tandai klaim dengan lonjakan signifikan dibanding periode sebelumnya.
   b. TREN KEBUTUHAN PER WILAYAH — kelompokkan pertanyaan dari kategori "lokasi posko" dan
      "bansos" berdasarkan wilayah yang disebut, hitung jumlah pertanyaan per wilayah per
      periode waktu, tandai wilayah dengan lonjakan pertanyaan dibanding periode sebelumnya.
3. Bangun halaman dashboard baru di Portal Relawan (reuse auth dari Fase 1) yang menampilkan:
   - Grafik sederhana (line/bar chart) untuk kedua insight di atas.
   - Daftar/badge "klaim sedang naik" dan "wilayah dengan lonjakan kebutuhan" sebagai ringkasan
     cepat di bagian atas dashboard.
4. PENTING — framing & penyajian: jangan tampilkan insight ini sebagai kepastian statistik
   (hindari kalimat seperti "klaim ini PASTI hoaks yang menyebar masif"). Tampilkan sebagai
   sinyal yang perlu ditindaklanjuti manusia (mis. label "Perlu perhatian" bukan "Confirmed
   trending"), karena ini data internal dari interaksi sistem kami sendiri, bukan data resmi
   penyebaran hoaks di masyarakat luas — selaras dengan prinsip kejujuran data yang sudah ada
   di Caveats proposal kami.
5. Karena kemungkinan trafik chat nyata saat demo masih sedikit, siapkan seeder TERPISAH berisi
   data log simulasi (jelas ditandai sebagai data simulasi/demo, bukan data live) khusus untuk
   mendemokan kapabilitas radar tren ini saat presentasi, supaya grafiknya terlihat representatif
   tanpa mengklaim itu data pengguna riil.
6. Uji dashboard ini end-to-end dan laporkan ke saya: tangkapan layar/penjelasan bagaimana radar
   tren menampilkan klaim & wilayah yang sedang naik berdasarkan data (termasuk data simulasi).

Batasan:
- Jangan mengubah skema/tool-calling inti RAG kecuali untuk kebutuhan ekstraksi wilayah yang
  sudah disebutkan di poin 1 — laporkan dulu sebelum eksekusi.
- Jangan menggabungkan data simulasi dengan data live secara tidak jelas — harus ada penandaan
  yang tegas (mis. kolom `is_simulated` pada data seeder demo) agar tidak tercampur saat
  ditampilkan, dan agar Anda bisa menjelaskan dengan jujur ke juri mana yang data simulasi.
```

---

## FASE 5 — Konteks Pembaruan untuk Proposal (Sesuai Panduan Teknis LKS) & Video Remotion

**Tujuan:** Setelah Fase 1-4 selesai dan teruji, minta Claude Code menyusun dokumen konteks perubahan — kali ini secara spesifik mencakup tiga fitur baru (Portal Relawan, Skrip Evaluasi, Radar Tren) — untuk dua tujuan: (a) menyesuaikan proposal agar selaras dengan rubrik/persyaratan di Panduan Ekshibisi KA LKS Dikmen Tingkat Nasional 2026, dan (b) memperbarui video pitch/demo yang sudah dibuat dengan Remotion.

```
PROMPT UNTUK CLAUDE CODE — FASE 5: DOKUMEN KONTEKS UNTUK PROPOSAL (SESUAI PANDUAN LKS) & VIDEO REMOTION

Konteks project:
"Cekarah" — sejak implementasi awal (streaming, skema 5 kategori intent, tool-calling, dataset,
peta) hingga tiga fitur tambahan yang baru selesai: (1) Portal Relawan dengan human-in-the-loop
curation (relawan bisa update data posko/bantuan/klaim secara langsung, ada review queue untuk
jawaban "belum ada data resmi", ada audit trail), (2) Skrip Evaluasi Otomatis (metrik aktual:
% jawaban bersitasi, akurasi klasifikasi intent, akurasi verifikasi klaim, keberhasilan menolak
pertanyaan out-of-scope, latency per kategori), (3) Radar Tren Hoaks & Kebutuhan (dashboard
agregasi yang mendeteksi lonjakan klaim/kebutuhan per wilayah dari log chat).

Saya butuh DUA dokumen ringkasan terpisah, untuk dua keperluan berbeda:

DOKUMEN A — `context-update-proposal-fitur-baru.md`
Tujuan: akan saya berikan ke Claude (chat, bukan Claude Code) untuk membantu menyesuaikan ulang
proposal solusi (Problem Canvas) kami, SECARA SPESIFIK supaya selaras dengan ketentuan & rubrik
resmi di Panduan Ekshibisi Kompetisi Kecerdasan Artifisial (KA) LKS Dikmen Tingkat Nasional 2026
— bukan hanya menjelaskan fitur baru secara teknis. Susun dokumen ini dengan eksplisit memetakan
setiap fitur baru ke poin-poin berikut dari panduan resmi (sebutkan poin panduan mana yang
relevan untuk tiap fitur):
1. Bagian "Target Pengguna" proposal — jelaskan bagaimana Portal Relawan sekarang benar-benar
   mengisi kebutuhan segmen "Relawan & organisasi kemanusiaan (PMI, MDMC, Tagana)" yang sebelumnya
   hanya tertulis di tabel tapi belum terlayani secara konkret di produk.
2. Bagian "Responsible AI" proposal — jelaskan bagaimana Portal Relawan (review queue + audit
   trail) memberi bukti KONKRET untuk syarat wajib panduan yang meminta "penjelasan di mana
   penilaian manusia tetap berperan dalam sistem" (bukan lagi sekadar kalimat naratif), serta
   bagaimana Radar Tren tetap menjaga framing yang jujur (sinyal untuk ditindaklanjuti manusia,
   bukan kepastian statistik) sesuai prinsip kejujuran data di Caveats kami.
3. Bagian "Rencana Sumber Data" / "Caveats" proposal — jelaskan implikasi adanya data hasil
   kurasi manual relawan (curated_claims) yang sekarang ikut menjadi bagian knowledge base RAG,
   dan implikasi adanya data simulasi pada Radar Tren (transparansi bahwa itu bukan data
   pengguna riil).
4. Bagian "Rekomendasi & Langkah Pengembangan Bertahap" proposal — gantikan tolok ukur yang
   sebelumnya berupa target/klaim (mis. "100% jawaban menyertakan sumber") dengan ANGKA AKTUAL
   hasil Skrip Evaluasi Otomatis (Fase 3), dan jelaskan ketegori mana yang sudah memenuhi target
   dan mana yang masih memerlukan perbaikan lanjutan secara jujur.
5. Tinjau ulang relevansi tiap fitur baru terhadap KRITERIA PENILAIAN RESMI di panduan (Pemahaman
   Masalah 20%, Kreativitas & Inovasi 20%, Pemanfaatan AI 20%, Responsible AI 15%, Fungsionalitas
   15%, Presentasi 10%) — untuk tiap fitur baru, sebutkan kriteria mana yang paling diperkuat dan
   berikan argumen ringkas yang bisa langsung dipakai sebagai narasi di proposal/saat tanya jawab.
   Tulis dalam bahasa Indonesia, terstruktur per bagian proposal asli, fokus pada substansi &
   implikasi terhadap penilaian — bukan log teknis/commit mentah.

DOKUMEN B — `context-update-video-remotion-fitur-baru.md`
Tujuan: akan saya berikan KEMBALI ke Claude Code (sesi terpisah) untuk memperbarui konten video
demo/pitch yang sebelumnya sudah dibuat dengan Remotion (termasuk yang sudah pernah diperbarui
sebelumnya, jika ada). Sebelum menyusun dokumen ini:
- Audit ulang project/folder Remotion yang ada di repo ini saat ini (struktur scene, komposisi,
  script narasi terbaru) dan laporkan ringkasannya sebagai bagian dari dokumen ini.
Isi dokumen ini harus mencakup:
1. Scene/segmen baru yang SEBAIKNYA ditambahkan untuk mendemokan tiga fitur baru ini secara
   spesifik, dengan alur yang jelas menunjukkan NILAI BARU (bukan cuma "fitur ada"), misalnya:
   - Demo alur human-in-the-loop secara end-to-end: chatbot jawab "belum ada data resmi" →
     muncul di review queue relawan → relawan menambah data → chatbot ditanya ulang → jawaban
     berubah dengan sumber baru. Ini sebaiknya jadi satu scene utuh karena paling kuat secara
     visual untuk membuktikan Responsible AI.
   - Tampilan Radar Tren dengan grafik lonjakan klaim/wilayah, dengan narasi yang menjelaskan
     posisi Cekarah sebagai "sistem deteksi dini", bukan cuma chatbot tanya-jawab.
   - Cuplikan ringkas hasil Skrip Evaluasi Otomatis (angka aktual) sebagai bukti kredibilitas,
     bisa ditampilkan sebagai overlay angka/slide ringkas, bukan rekaman proses run script-nya.
2. Untuk setiap scene yang sudah ada di project Remotion saat ini, tandai: TETAP PAKAI / PERLU
   DIPERBARUI / PERLU DIHAPUS, beserta alasannya, dan usulan konten pengganti jika diperlukan.
3. Usulan storyboard baru secara keseluruhan (scene → apa yang ditampilkan → poin narasi kunci),
   memastikan durasi tetap realistis sesuai ketentuan video pitch (2-5 menit sesuai panduan resmi
   Tahap 1 Babak Seleksi Daring).
4. Tulis dalam format deskriptif per scene yang actionable untuk eksekusi teknis di Remotion,
   TIDAK perlu langsung menulis kode komposisi pada dokumen ini — implementasi kode akan diminta
   secara terpisah setelah saya review storyboard-nya.

Tugas Anda untuk fase ini:
1. Audit ringkas seluruh perubahan dari Fase 1-4 di file prompt ini (boleh merujuk riwayat
   commit/PR jika ada).
2. Audit project Remotion yang sudah ada (struktur folder, scene, narasi terbaru) sebelum menulis
   Dokumen B.
3. Hasilkan dua file terpisah: `context-update-proposal-fitur-baru.md` dan
   `context-update-video-remotion-fitur-baru.md` sesuai spesifikasi di atas.
4. Tampilkan isi kedua file tersebut kepada saya untuk saya review sebelum saya teruskan masing-
   masing ke "konsumen" yang dituju (Claude untuk Dokumen A, Claude Code sesi baru untuk
   Dokumen B).

Batasan:
- Jangan langsung mengubah file proposal asli maupun project Remotion pada fase ini — fase ini
  HANYA menghasilkan dua dokumen konteks/ringkasan, eksekusi perubahan aktualnya dilakukan di
  sesi/perintah terpisah setelah saya review.
```

---

## Catatan Tambahan untuk Anda (bukan untuk Claude Code)

- **Urutan eksekusi yang disarankan:** Fase 1 → Fase 2 → (Fase 3 bisa kapan saja paralel, bahkan sebelum Fase 1 selesai jika Anda punya kapasitas tim mengerjakan paralel) → Fase 4 → Fase 5 paling akhir.
- **Fase 5 sengaja menunggu semuanya selesai** karena dua dokumen keluarannya (proposal & video) seharusnya mencerminkan kondisi akhir sistem, bukan kondisi parsial — kalau dijalankan terlalu awal, Anda berisiko merevisi proposal/video dua kali.
- **Soal Dokumen A khusus untuk panduan LKS:** saya minta Claude Code memetakan tiap fitur ke poin spesifik panduan resmi (bukan cuma penjelasan teknis umum) supaya saat Anda memberikan dokumen ini ke Claude untuk revisi proposal, hasilnya benar-benar selaras dengan bahasa & struktur yang dipakai panduan/rubrik — ini akan membuat proposal terasa "menjawab langsung" rubrik, bukan sekadar dokumen teknis terpisah dari kriteria penilaian.
