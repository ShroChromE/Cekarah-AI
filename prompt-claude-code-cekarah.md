# Prompt Bertahap untuk Claude Code — Pengembangan Cekarah

> **Cara pakai:** Dokumen ini berisi **5 fase independen**. Copy isi satu blok "PROMPT UNTUK CLAUDE CODE" saja per sesi, jangan digabung. Tunggu fase sebelumnya selesai & teruji sebelum lanjut ke fase berikutnya. Setiap blok sudah menyertakan konteks project secara ringkas supaya Claude Code tidak perlu membaca ulang dokumen ini.

---

## Konteks Umum Project (untuk referensi Anda, tidak perlu di-copy)

- **Nama produk:** Cekarah — asisten AI percakapan untuk (1) navigasi bantuan darurat/sosial dan (2) verifikasi klaim/hoaks pascabencana.
- **Stack:** Laravel 13 + Laravel AI SDK native (orkestrasi prompt, tool-calling) → Gemini API. Frontend: Inertia.js + React. DB: PostgreSQL + pgvector.
- **Status saat ini:** Aplikasi dasar (chat + RAG) sudah jalan, tapi:
  1. Respons chat masih *blocking* — UI baru render setelah seluruh generation selesai.
  2. Belum ada *routing* otomatis ke 5 skenario intent (info bencana, verifikasi klaim, lokasi posko + maps, info bantuan/bansos, di luar konteks → tolak).
  3. Skema database belum dirancang untuk menampung 5 skenario itu.
  4. Belum ada dataset (sintetis tapi bersumber valid & punya link sitasi) untuk mengisi skenario tersebut.
  5. Ingin pakai *tool-calling* dari Laravel AI SDK — satu tool per kategori intent.

---

## FASE 1 — Streaming Response (Chat Tidak Lagi Blocking)

**Tujuan:** Saat ini balasan chatbot baru muncul setelah seluruh response selesai digenerate. Ubah agar token/kalimat muncul secara *streaming* (incremental), mirip ChatGPT/Claude, supaya user tidak menunggu lama tanpa feedback visual.

```
PROMPT UNTUK CLAUDE CODE — FASE 1: STREAMING RESPONSE

Konteks project:
Saya membangun "Cekarah", chatbot AI berbasis Laravel 13 + Laravel AI SDK (native, untuk
orkestrasi prompt & tool-calling) yang memanggil Gemini API, dengan frontend Inertia.js + React.
Saat ini implementasi chat masih bersifat blocking: backend menunggu seluruh response AI selesai
digenerate sebelum mengirim balasan ke frontend, sehingga user harus menunggu lama tanpa ada
indikasi progres.

Tugas Anda HANYA untuk fase ini (jangan kerjakan hal lain di luar scope ini):
1. Audit dulu implementasi chat yang sudah ada saat ini:
   - Temukan controller/route/service yang menangani request chat ke Gemini via Laravel AI SDK.
   - Temukan komponen React yang menampilkan chat (message list, input box).
   - Laporkan temuan Anda dalam bentuk ringkasan sebelum melakukan perubahan apa pun.
2. Setelah saya konfirmasi pemahaman Anda benar, implementasikan streaming response end-to-end:
   - Backend: gunakan kapabilitas streaming dari Laravel AI SDK untuk memanggil Gemini secara
     streaming, lalu alirkan token/chunk tersebut ke client menggunakan Server-Sent Events (SSE)
     atau chunked HTTP response (pilih pendekatan yang paling kompatibel dengan Inertia.js;
     jika Inertia tidak mendukung SSE secara native, gunakan endpoint API biasa di luar siklus
     Inertia khusus untuk streaming chat, dan jelaskan trade-off-nya ke saya sebelum implementasi).
   - Frontend (React): konsumsi stream tersebut (EventSource atau fetch dengan ReadableStream),
     lalu render teks secara incremental ke bubble chat AI (efek "mengetik").
   - Tambahkan state loading/typing indicator sebelum token pertama tiba.
   - Tangani error/disconnect stream dengan graceful fallback (tampilkan pesan error yang ramah,
     bukan crash atau hang).
   - Pastikan riwayat percakapan (chat history) tetap tersimpan dengan benar ke database SETELAH
     full response selesai distream (jangan menyimpan partial response sebagai pesan final).
3. Jangan mengubah logika RAG/retrieval yang sudah ada — fokus murni pada mekanisme delivery
   (streaming), bukan isi jawaban.
4. Setelah implementasi, berikan ringkasan file apa saja yang diubah/ditambahkan dan cara saya
   melakukan test manual (langkah-langkah testing di browser).

Batasan:
- Jangan menyentuh skema database, tabel baru, atau tool-calling AI SDK — itu di luar scope fase ini.
- Jangan melakukan refactor besar di luar yang diperlukan untuk streaming.
```

---

## FASE 2 — Desain & Migrasi Skema Database (RESTful, Bahasa Inggris)

**Tujuan:** Merancang tabel-tabel baru/penyesuaian tabel lama agar bisa menangani 5 skenario intent. Diminta **analisis dulu**, baru eksekusi migration setelah Anda setuju.

```
PROMPT UNTUK CLAUDE CODE — FASE 2: DESAIN SKEMA DATABASE

Konteks project:
"Cekarah" adalah chatbot AI (Laravel 13 + Laravel AI SDK + Gemini, PostgreSQL + pgvector,
Inertia.js + React) untuk dua kapabilitas: (1) navigasi bantuan darurat/sosial, (2) verifikasi
klaim/hoaks. Sistem akan dikembangkan agar bisa secara otomatis mengklasifikasi setiap pertanyaan
user ke salah satu dari 5 kategori intent berikut, lalu merespons sesuai kategori:

1. INFORMASI UMUM — pertanyaan seputar info bencana/situasi terkini (mis. "banjir terjadi di
   mana saja?") → jawab berbasis data + sitasi sumber resmi.
2. VERIFIKASI KLAIM — user menempel/menyampaikan klaim atau kabar (mis. "kata teman saya akan
   ada banjir besar hari ini, benar tidak?") → sistem memberi status valid/tidak + penjelasan +
   sumber.
3. LOKASI POSKO/BANTUAN FISIK — pertanyaan lokasi posko, dapur umum, shelter, dll (mis. "posko
   pengungsian di Binjai di mana?") → jawaban teks + data titik lokasi (lat/long) untuk
   ditampilkan di peta pada frontend.
4. BANTUAN SOSIAL/BANSOS — pertanyaan tentang bantuan yang tersedia di suatu daerah/kondisi
   (mis. "daerah saya di Binjai kena bencana, ada bantuan apa?") → jelaskan jenis bantuan, dari
   siapa, apa isinya, status/jadwalnya.
5. DI LUAR KONTEKS — pertanyaan tidak berkaitan (mis. "siapa presiden Indonesia saat ini?") →
   sistem menolak dengan sopan dan mengarahkan kembali ke topik yang didukung.

Setiap jawaban kategori 1-4 wajib bisa menyertakan referensi sumber (nama sumber, URL, tanggal)
yang bisa diklik oleh user di frontend.

Tugas Anda HANYA untuk fase ini:
1. JANGAN langsung menulis migration. Lakukan dulu:
   a. Audit skema database yang sudah ada saat ini (migrations, model Eloquent, termasuk tabel
      untuk chat history dan knowledge base/RAG embedding jika sudah ada).
   b. Berdasarkan 5 skenario di atas, susun PROPOSAL desain skema database baru/perubahan,
      mencakup minimal:
      - Tabel untuk menyimpan sumber resmi/referensi (source/citation) yang reusable di semua
        kategori (nama sumber, url, tanggal publikasi, kategori sumber, dsb).
      - Tabel untuk data informasi bencana/situasi terkini (kategori 1).
      - Tabel untuk hasil/record verifikasi klaim (kategori 2) — termasuk status enum
        (verified / unverified / hoax / no_official_data), klaim asli user, penjelasan.
      - Tabel untuk lokasi posko/shelter dengan kolom koordinat (latitude, longitude), alamat,
        kapasitas, jenis posko, status aktif (kategori 3).
      - Tabel untuk program bantuan sosial/bansos (kategori 4) — penyedia bantuan, jenis bantuan,
        target wilayah, status/jadwal penyaluran, dan relasinya ke event bencana/lokasi jika
        relevan.
      - Tabel/kolom untuk mencatat kategori intent pada setiap pesan chat (untuk kategori 5 cukup
        dicatat sebagai log, tidak butuh tabel data tersendiri).
      - Pertimbangkan relasi antar tabel (mis. posko & bansos terkait ke satu "disaster event"
        yang sama; semua data punya referensi ke tabel source/citation).
      - Pertimbangkan kebutuhan kolom embedding (pgvector) bila tabel tersebut juga akan dipakai
        sebagai bagian dari retrieval RAG.
   c. Seluruh penamaan tabel & kolom WAJIB:
      - Berbahasa Inggris.
      - Mengikuti konvensi RESTful/Laravel standar (snake_case, nama tabel plural, foreign key
        `xxx_id`, timestamps standar, dsb).
   d. Sajikan proposal ini dalam bentuk:
      - Daftar tabel beserta deskripsi singkat fungsinya.
      - Untuk setiap tabel: daftar kolom, tipe data, nullable/not null, constraint, relasi (FK).
      - Diagram relasi sederhana dalam bentuk teks (mis. "shelter_locations.disaster_event_id ->
        disaster_events.id").
2. STOP setelah menyajikan proposal di atas. Tunggu konfirmasi/revisi dari saya.
3. Setelah saya approve, baru buat migration files, model Eloquent (dengan relasi yang sesuai),
   dan (jika relevan) factory/seeder kosong sebagai kerangka untuk fase berikutnya.
4. Jangan mengisi data dummy/sintetis pada fase ini — itu scope fase berikutnya.

Batasan:
- Jangan mengubah logika chat/streaming dari fase sebelumnya.
- Jangan implementasi tool-calling AI SDK — itu scope fase berikutnya.
```

---

## FASE 3 — Intent Routing & Tool-Calling (Laravel AI SDK Tools)

**Tujuan:** Sistem otomatis menentukan kategori 1–5 dari setiap pertanyaan, lalu memanggil *tool* yang sesuai (satu tool per kategori) menggunakan kapabilitas tool-calling dari Laravel AI SDK.

```
PROMPT UNTUK CLAUDE CODE — FASE 3: INTENT ROUTING + TOOL-CALLING

Konteks project:
"Cekarah" (Laravel 13 + Laravel AI SDK + Gemini, Inertia.js + React, PostgreSQL + pgvector).
Skema database untuk 5 kategori intent (info bencana, verifikasi klaim, lokasi posko, bansos,
di luar konteks) sudah dirancang & dimigrasi pada fase sebelumnya (sebutkan ke saya jika Anda
perlu saya tunjukkan ulang nama tabel/model finalnya sebelum mulai).

Tujuan fase ini: sistem harus secara OTOMATIS menentukan kategori pertanyaan user, lalu memanggil
tool yang tepat — TANPA saya harus menentukan kategori secara manual. Gunakan kapabilitas native
tool-calling / function-calling dari Laravel AI SDK (jangan membuat classifier terpisah secara
manual dengan if-else keyword matching sebagai mekanisme utama; manfaatkan reasoning model Gemini
melalui tool schema yang deskriptif, agar pemilihan tool dilakukan oleh model itu sendiri).

Lima kategori & tool yang harus dibuat (1 tool per kategori):
1. `search_disaster_info` — untuk pertanyaan informasi umum bencana/situasi terkini.
   Input: query pencarian (string), opsional filter wilayah/tanggal.
   Output: ringkasan info + daftar referensi (sumber, url, tanggal).
2. `verify_claim` — untuk memverifikasi klaim/kabar yang disampaikan user.
   Input: teks klaim yang ingin diverifikasi.
   Output: status (verified/unverified/hoax/no_official_data), penjelasan, referensi.
3. `find_shelter_locations` — untuk pencarian lokasi posko/shelter/dapur umum.
   Input: nama wilayah/kota, opsional jenis posko.
   Output: daftar lokasi dengan nama, alamat, latitude, longitude, kapasitas/catatan, referensi —
   format output harus terstruktur agar mudah dipakai frontend untuk menampilkan peta (lihat
   fase berikutnya untuk integrasi UI peta).
4. `get_aid_assistance_info` — untuk pertanyaan bantuan sosial/bansos di suatu wilayah/kondisi.
   Input: wilayah dan/atau jenis bencana/kondisi.
   Output: daftar program bantuan (penyedia, jenis bantuan, status/jadwal, syarat singkat),
   referensi.
5. TIDAK perlu tool untuk kategori "di luar konteks" — instruksikan melalui system prompt model
   agar model TIDAK memanggil tool apa pun bila pertanyaan tidak relevan dengan ke-4 kategori di
   atas, dan langsung merespons dengan kalimat penolakan sopan yang mengarahkan user kembali ke
   topik yang didukung oleh Cekarah (jangan hardcode daftar topik terlarang; biarkan model
   menentukan relevansi berdasarkan deskripsi scope aplikasi di system prompt).

Tugas Anda untuk fase ini:
1. Audit dulu bagaimana Laravel AI SDK di project ini saat ini mendefinisikan & mendaftarkan
   tools/function-calling (jika belum ada implementasi tool sama sekali, jelaskan ke saya pola
   yang akan Anda pakai sebelum eksekusi).
2. Implementasikan ke-4 Tool class di atas:
   - Setiap tool punya nama, deskripsi yang jelas (untuk membantu model memilih tool yang tepat),
     dan skema input/parameter yang valid.
   - Setiap tool melakukan query ke tabel-tabel terkait dari fase sebelumnya (gunakan Eloquent
     model yang sudah ada). Untuk kategori yang butuh similarity search (mis. info bencana atau
     verifikasi klaim berbasis RAG), manfaatkan pgvector + embedding sesuai pola RAG yang sudah
     ada di project.
   - Pastikan setiap output tool MENYERTAKAN referensi sumber (nama, url, tanggal) secara
     terstruktur (bukan hanya teks bebas), supaya frontend bisa render sebagai link yang bisa
     diklik.
3. Update system prompt orkestrasi Gemini agar:
   - Menjelaskan scope aplikasi Cekarah dengan jelas (4 kapabilitas + larangan menjawab di luar
     scope).
   - Mendorong model memanggil tool yang sesuai berdasarkan intent, bukan menjawab dari
     pengetahuan umum model.
   - Instruksikan model untuk SELALU menyertakan sumber + tanggal di jawaban final, dan menjawab
     "belum ada data resmi" jika tool tidak menemukan hasil relevan (selaras dengan prinsip
     Responsible AI proposal kami: anti-halusinasi, grounding ke sumber).
4. Tambahkan logging: simpan kategori intent yang terdeteksi (tool mana yang dipanggil, atau
   "out_of_scope" bila tidak ada tool dipanggil) ke kolom/tabel log chat history yang sudah
   disiapkan di fase sebelumnya — ini untuk kebutuhan analitik & demo ke juri.
5. Berikan ringkasan: tool apa saja yang dibuat, file yang diubah, serta beberapa contoh prompt
   uji manual untuk masing-masing dari 5 kategori (gunakan contoh pertanyaan yang relevan dengan
   konteks bencana Sumatera/Binjai sesuai proposal kami) agar saya bisa test satu per satu.

Batasan:
- Jangan mengisi dataset/data dummy final — itu scope fase berikutnya (fase ini cukup pastikan
  tool bisa query ke tabel yang ada, walau datanya masih kosong/minim untuk testing fungsi tool).
- Jangan mengubah skema database lagi di fase ini; jika Anda menemukan kebutuhan kolom tambahan,
  laporkan ke saya dulu sebelum mengubah migration.
```

---

## FASE 4 — Dataset Sintetis Bersumber Valid (Seeder)

**Tujuan:** Mengisi tabel-tabel dari Fase 2 dengan data sintetis/dummy yang **dibangun dari sumber resmi yang valid** (bukan data asal-asalan), lengkap dengan referensi yang bisa diklik, dan mencakup semua skenario uji yang Anda sebutkan (banjir, posko Binjai, verifikasi klaim, bansos Binjai, dst).

```
PROMPT UNTUK CLAUDE CODE — FASE 4: DATASET SINTETIS BERSUMBER VALID

Konteks project:
"Cekarah" (Laravel 13 + Laravel AI SDK + Gemini, Inertia.js + React, PostgreSQL + pgvector).
Skema database (fase 2) dan tool-calling per kategori intent (fase 3) sudah selesai. Sekarang
tabel-tabel tersebut perlu diisi data uji yang realistis.

Ketentuan panitia (WAJIB dipatuhi):
- Dataset boleh berupa data sintetis/dummy buatan sendiri (bukan data pribadi/sensitif asli).
- Namun setiap entri data WAJIB merujuk pada/terinspirasi dari sumber resmi yang benar-benar
  valid dan bisa dicek (mis. BNPB, BMKG, Komdigi, Kemensos/cekbansos, PMI, MAFINDO/turnbackhoax,
  Ombudsman RI) — setiap baris data harus punya field referensi (nama sumber, url, tanggal) yang
  REALISTIS, bukan url palsu/asal ketik.
- Jangan mengarang angka spesifik yang mengklaim sebagai data resmi pasti benar; tandai jelas
  sebagai data simulasi/ilustrasi pada metadata bila diperlukan, agar transparan secara etik
  (selaras prinsip Responsible AI proposal kami: anti-halusinasi & kejujuran data).

Skenario uji yang dataset ini WAJIB bisa menjawab (gunakan ini sebagai acuan minimum coverage):
1. "Banjir sedang terjadi di mana saja?" → harus ada beberapa entri info bencana/banjir aktif
   (boleh berbasis konteks bencana hidrometeorologi Sumatera 2025–2026) dengan sumber & tanggal.
2. "Lokasi posko pengungsian di Binjai?" → minimal 2-3 entri posko/shelter di wilayah Binjai
   dengan nama, alamat, latitude/longitude valid (boleh koordinat asli wilayah Binjai), kapasitas
   /cakupan KK terdampak, dan sumber (mis. "BNPB, 27 Nov 2025").
3. Klaim untuk diuji verifikasi, contoh: "Akan terjadi banjir besar hari ini" → siapkan minimal
   1-2 entri referensi/fakta resmi yang bisa dipakai tool verifikasi untuk menentukan status
   (verified/unverified/hoax/no_official_data) atas klaim semacam itu, termasuk contoh kasus
   hoaks nyata yang relevan (mis. kasus "air laut naik" di Pidie Jaya, Aceh) sebagai referensi
   pola hoaks bencana.
4. "Daerah saya di Binjai kena bencana, ada bantuan apa?" → minimal 1-2 entri program
   bantuan/bansos untuk wilayah Binjai (penyedia, jenis bantuan, status/jadwal, syarat singkat),
   terhubung ke event bencana yang relevan.
5. Pertanyaan di luar konteks (mis. "siapa presiden Indonesia saat ini?") → TIDAK butuh dataset,
   cukup pastikan tidak ada data yang membuat tool ter-trigger untuk pertanyaan semacam ini.

Tugas Anda untuk fase ini:
1. Buat database seeder (Laravel Seeder/Factory, bukan hardcode di tool) untuk seluruh tabel
   terkait dari fase 2: source/citation, disaster events/info, shelter locations, aid programs,
   dan data pendukung verifikasi klaim.
2. Pastikan data saling terhubung secara logis (mis. posko di Binjai terhubung ke disaster event
   bencana hidrometeorologi Sumatera yang sama, bantuan bansos juga terhubung ke event yang sama).
3. Jika tabel terkait juga dipakai untuk RAG (butuh embedding/pgvector), generate embedding untuk
   setiap entri teks menggunakan pipeline embedding yang sudah ada di project (jangan biarkan
   kolom vector kosong).
4. Jalankan seeder dan tunjukkan hasilnya (jumlah baris per tabel) sebagai verifikasi.
5. Lakukan uji coba manual ke-5 skenario di atas melalui chat (gunakan tool dari fase 3) dan
   laporkan hasil tanya-jawabnya ke saya — termasuk apakah sumber/referensi muncul dengan benar
   di setiap jawaban.

Batasan:
- Jangan ubah skema/migration lagi pada fase ini kecuali ada kolom yang benar-benar belum cukup
  untuk menampung data (laporkan dulu ke saya sebelum mengubah).
- Jangan ubah logika tool-calling dari fase 3, kecuali untuk memperbaiki bug yang ditemukan saat
  testing data baru — jika ada perbaikan bug, laporkan apa yang diubah dan kenapa.
```

---

## FASE 5 — Integrasi UI Peta & Pengujian End-to-End

**Tujuan:** Memastikan jawaban kategori "lokasi posko" tampil sebagai peta interaktif di frontend (seperti contoh tangkapan layar yang Anda lampirkan), dan seluruh 5 skenario teruji end-to-end dari sisi UI, bukan hanya backend.

```
PROMPT UNTUK CLAUDE CODE — FASE 5: INTEGRASI UI PETA + PENGUJIAN END-TO-END

Konteks project:
"Cekarah" (Laravel 13 + Laravel AI SDK + Gemini, Inertia.js + React, PostgreSQL + pgvector).
Streaming chat (fase 1), skema DB (fase 2), tool-calling per kategori intent (fase 3), dan
dataset sintetis bersumber valid (fase 4) sudah selesai dan sudah diuji di level backend/chat
teks. Fase ini fokus pada penyempurnaan tampilan frontend, khususnya untuk kategori "lokasi
posko/shelter".

Tugas Anda untuk fase ini:
1. Untuk respons dari tool `find_shelter_locations` (kategori posko), pastikan frontend React
   menampilkan:
   - Teks penjelasan dari AI seperti biasa di bubble chat.
   - Komponen peta interaktif (gunakan library peta yang sudah/akan dipakai di project, mis.
     Leaflet/Google Maps/Mapbox — tanyakan ke saya dulu library mana yang ingin dipakai bila
     belum ada keputusan) yang menampilkan marker untuk setiap lokasi posko dari hasil tool,
     dengan info popup berisi nama posko, alamat, kapasitas/catatan, dan sumber data.
   - Jika ada lebih dari satu lokasi, sediakan navigasi antar-marker (mis. tombol next/prev atau
     daftar lokasi yang bisa diklik untuk fokus ke marker terkait di peta).
2. Pastikan referensi/sumber (nama sumber + url + tanggal) yang dikembalikan oleh SEMUA tool
   (bukan hanya posko) ditampilkan di UI sebagai elemen yang bisa diklik (link keluar ke sumber
   resmi), konsisten di semua kategori jawaban.
3. Lakukan pengujian end-to-end untuk seluruh 5 skenario berikut LANGSUNG dari UI chat (bukan
   hanya backend), dan laporkan hasilnya ke saya dengan screenshot/penjelasan per skenario:
   a. Pertanyaan info bencana umum.
   b. Pertanyaan verifikasi klaim.
   c. Pertanyaan lokasi posko (pastikan peta tampil dengan benar).
   d. Pertanyaan bantuan/bansos.
   e. Pertanyaan di luar konteks (pastikan direspons sebagai penolakan yang sopan, bukan
      dijawab dari pengetahuan umum model).
4. Catat bug/isu apa pun yang ditemukan selama pengujian end-to-end, perbaiki, dan tunjukkan
   ringkasan akhir kesiapan aplikasi (apa yang sudah berfungsi penuh, apa yang masih perlu
   perbaikan lanjutan) — ringkasan ini akan saya pakai sebagai bahan video demo/pitch ke juri.

Batasan:
- Jangan menambah kategori/tool baru di luar 4 tool yang sudah ada.
- Jangan mengubah skema database, kecuali ada bug data yang ditemukan saat testing — jika ada,
  laporkan dulu sebelum mengubah.
```

---

## FASE 6 — Ringkasan Konteks Perubahan (untuk Update Proposal & Video Remotion)

**Tujuan:** Setelah Fase 1–5 selesai dan teruji, minta Claude Code menyusun **dokumen konteks perubahan** dalam dua versi: (a) untuk diberikan ke Claude (chat) agar bisa membantu menyesuaikan isi proposal, dan (b) untuk diberikan kembali ke Claude Code agar bisa memperbarui konten video yang sudah dibuat sebelumnya dengan Remotion. Jalankan fase ini setelah seluruh fase teknis (1–5) selesai, bukan di tengah jalan.

```
PROMPT UNTUK CLAUDE CODE — FASE 6: DOKUMEN KONTEKS PERUBAHAN (PROPOSAL & VIDEO REMOTION)

Konteks project:
"Cekarah" (Laravel 13 + Laravel AI SDK + Gemini, Inertia.js + React, PostgreSQL + pgvector).
Seluruh implementasi teknis berikut sudah selesai dan teruji: (1) streaming response chat,
(2) skema database untuk 5 kategori intent, (3) tool-calling per kategori intent via Laravel AI
SDK, (4) dataset sintetis bersumber valid untuk tiap kategori, (5) integrasi UI peta untuk hasil
lokasi posko + pengujian end-to-end seluruh 5 skenario.

Saya butuh DUA dokumen ringkasan terpisah dari seluruh perubahan ini, karena akan dipakai untuk
dua keperluan berbeda oleh dua "konsumen" yang berbeda:

DOKUMEN A — `context-update-proposal.md`
Tujuan: akan saya berikan ke Claude (chat, bukan Claude Code) untuk membantu menyesuaikan ulang
dokumen proposal solusi (Problem Canvas) kami yang sudah ada. Susun dokumen ini agar SELARAS
dengan struktur proposal asli (latar belakang, target pengguna, ide solusi berbasis AI,
pendekatan teknis & arsitektur AI, rencana sumber data, responsible AI, roadmap/tahapan
pengembangan, caveats), dengan isi:
1. Apa saja yang BERUBAH atau BERTAMBAH konkret dari rencana awal proposal ke implementasi
   aktual saat ini (mis. mekanisme tool-calling per kategori yang sekarang benar-benar ada &
   nama-nama tool-nya, skema database final beserta nama tabel utamanya, cara dataset
   sintetis-bersumber-valid disusun, mekanisme streaming response).
2. Untuk setiap perubahan, jelaskan dampaknya terhadap klaim/narasi yang ada di proposal lama
   (apakah memperkuat klaim Responsible AI/grounding, apakah mengubah arsitektur RAG yang
   dijelaskan, apakah ada bagian "Rekomendasi & Langkah Pengembangan Bertahap" yang perlu
   diperbarui karena tahap yang direncanakan sudah tercapai).
3. Sertakan poin-poin yang SEBAIKNYA ditambahkan ke bagian "Responsible AI" dan "Caveats" pada
   proposal, berdasarkan keterbatasan nyata yang ditemukan saat implementasi (mis. potensi
   misklasifikasi intent, sifat data sintetis pada dataset, ketergantungan pada Gemini tool-
   calling).
4. Tulis dalam bahasa Indonesia, gaya ringkas dan terstruktur per bagian proposal (gunakan
   heading yang sama dengan struktur proposal asli), BUKAN dalam bentuk log commit/teknis
   mentah — dokumen ini akan dibaca oleh AI lain untuk membantu menulis ulang proposal, jadi
   fokus pada substansi & implikasinya, bukan detail kode.

DOKUMEN B — `context-update-video-remotion.md`
Tujuan: akan saya berikan KEMBALI ke Claude Code (sesi terpisah) untuk memperbarui konten video
demo/pitch yang sebelumnya sudah dibuat menggunakan Remotion. Sebelum menyusun dokumen ini:
- Cari & audit project/folder Remotion yang sudah ada di repo ini (struktur scene, komposisi,
  script narasi, aset yang dipakai) dan laporkan ringkasannya ke saya sebagai bagian dari
  dokumen ini, supaya saya tahu apa yang akan terdampak.
Isi dokumen ini harus mencakup:
1. Fitur/alur baru apa saja yang SEBAIKNYA ditambahkan atau diganti di video, berdasarkan
   kapabilitas yang sekarang benar-benar berfungsi (mis. demo streaming chat yang terasa lebih
   responsif, demo otomatis sistem memilih kategori intent yang tepat tanpa user menentukan
   manual, demo peta interaktif untuk lokasi posko, demo penolakan untuk pertanyaan di luar
   konteks).
2. Untuk setiap scene/bagian video yang sudah ada, tandai: TETAP PAKAI / PERLU DIPERBARUI /
   PERLU DIHAPUS, beserta alasannya, dan usulan konten pengganti jika "perlu diperbarui".
3. Usulan urutan alur demo yang baru (storyboard ringkas: scene → apa yang ditampilkan → poin
   narasi kunci), yang mencerminkan 5 skenario uji end-to-end yang sudah berhasil dijalankan di
   Fase 5 (info bencana, verifikasi klaim, lokasi posko + peta, bansos, penolakan di luar
   konteks).
4. Tulis dalam format yang actionable untuk eksekusi teknis di Remotion (cukup deskriptif per
   scene, TIDAK perlu langsung menulis kode komposisi Remotion pada dokumen ini — implementasi
   kode akan diminta secara terpisah setelah saya review storyboard-nya).

Tugas Anda untuk fase ini:
1. Audit ringkas seluruh perubahan dari Fase 1–5 (boleh merujuk ke riwayat commit/PR jika ada).
2. Audit project Remotion yang sudah ada (struktur folder, scene, narasi) sebelum menulis
   Dokumen B.
3. Hasilkan dua file terpisah: `context-update-proposal.md` dan `context-update-video-remotion.md`
   sesuai spesifikasi di atas.
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

- **Soal kriteria juri (Responsible AI 15%):** pastikan saat Fase 4/5, Anda juga menyiapkan narasi mitigasi risiko dataset sintetis ini untuk proposal/pitch — yaitu "data sintetis dibangun dari pola sumber resmi yang valid, transparan, dan bukan data pribadi", karena ini akan ditanya juri saat sesi tanya jawab.
- **Soal Fase 3 (tool-calling otomatis):** ada risiko model salah pilih tool atau memanggil tool yang tidak relevan (misklasifikasi intent). Saat testing Fase 3 dan 4, sengaja coba beberapa pertanyaan ambigu (mis. campuran info + lokasi dalam satu kalimat) untuk melihat bagaimana model menangani, dan siapkan fallback/penjelasan untuk itu di demo.
- **Soal urutan eksekusi:** Fase 2 dan 3 paling krusial untuk skor "Pemanfaatan AI yang efektif & tepat guna" dan "Fungsionalitas aplikasi" — prioritaskan keduanya selesai dengan baik sebelum terlalu banyak waktu di Fase 5 (polish UI).
- **Soal Fase 6:** ini sengaja dipisah jadi tahap tersendiri (bukan ditempel di akhir Fase 5) karena dua dokumen keluarannya punya pembaca yang berbeda (Claude untuk proposal, Claude Code lagi untuk Remotion) — review dulu isi kedua dokumen sebelum diteruskan, supaya tidak ada asumsi teknis Claude Code yang salah ikut terbawa ke revisi proposal, dan sebaliknya tidak ada framing pitch/marketing yang salah ikut menjadi instruksi teknis ke Remotion.
