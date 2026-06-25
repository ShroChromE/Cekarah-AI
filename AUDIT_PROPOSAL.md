# AUDIT_PROPOSAL.md — Audit Aktual Aplikasi Cekarah

> Disusun read-only dari kode & database aktual (bukan rencana/klaim) untuk bahan proposal & naskah video pitch LKS Dikmen KA Nasional 2026. Tanggal audit: 2026-06-25.
>
> **Legenda kejujuran:** `[ADA]` = terbukti di kode · `[SEBAGIAN]` = ada tapi parsial · `[BELUM ADA DI KODE]` = baru rencana/tidak ditemukan · `[SUMBER TIDAK TERIDENTIFIKASI]` = tidak bisa dipastikan dari kode.

---

## 1. Ringkasan Aplikasi

**Nama:** Cekarah — asisten AI berbasis chat untuk warga Indonesia dalam situasi bencana.

**Deskripsi fungsi utama (disimpulkan dari kode; tidak ada README khusus produk):** Warga mengetik pertanyaan ke satu kotak chat publik tanpa login. Agen AI (Gemini, function-calling) mengklasifikasikan kebutuhan ke 4 kategori dan memanggil tool yang sesuai — informasi bencana, verifikasi klaim/hoaks, lokasi posko (dengan peta), dan bantuan sosial — lalu menjawab dengan grounding ke data internal + riset web langsung, selalu menyertakan sumber. Di sisi lain ada **Portal Relawan** (login admin/volunteer) untuk kurasi data (human-in-the-loop), antrean tinjauan jawaban "belum ada data resmi", dan **Radar Tren** (agregasi log untuk mendeteksi lonjakan klaim/kebutuhan).

**Stack teknologi aktual (versi dari `composer.json` & `package.json`):**

| Lapis | Teknologi & versi |
|---|---|
| Bahasa server | PHP `^8.3` (lingkungan dev: 8.5) |
| Framework | Laravel `^13.7` |
| AI SDK | Laravel AI (`laravel/ai` via agen `Laravel\Ai\*`) — provider Gemini |
| Auth | Laravel Fortify `^1.37.2` |
| SPA bridge | Inertia.js Laravel `^3.0` + `@inertiajs/react` `^3.0.0` |
| Frontend | React `^19.2.0`, TypeScript `^5.7.2` |
| Styling | Tailwind CSS `^4.0.0` |
| Build | Vite `^8.0.0`, `@vitejs/plugin-react` `^5.2.0` |
| Routing typed | Laravel Wayfinder `^0.1.14` |
| DB | PostgreSQL (kolom `embedding` bertipe `jsonb`) |
| Peta | Leaflet 1.9.4 — **dimuat via CDN runtime** di `resources/js/components/ShelterMap.tsx`, BUKAN dependency npm |
| Test | PHPUnit `^12.5.23`, Larastan `^3.9`, Pint `^1.27` |

> ✅ **pgvector AKTIF (diperbaiki 2026-06-25).** Kolom `embedding` pada `knowledge_chunks`, `claim_verifications`, dan `disaster_events` kini bertipe **`vector(3072)`** (migrasi `2026_06_25_040000_convert_embeddings_to_pgvector`). Kemiripan kosinus dihitung **di PostgreSQL** via operator `<=>` di `app/Services/KnowledgeIndexer.php::similarChunks` & `app/Services/ClaimIndexer.php::similar` (`1 - (embedding <=> ?::vector)`). Cast `app/Casts/Vector.php` menjembatani array PHP ↔ literal pgvector. Klaim "pgvector" di proposal kini **benar/aktual**. (Tanpa indeks ANN — dataset kecil, sequential scan; pgvector ANN dibatasi 2000 dim.)

---

## 2. Pemetaan ke Rubrik Penilaian LKS

| Kriteria Rubrik (Bobot) | Bukti di Kode | Gap yang Teridentifikasi |
|---|---|---|
| **Pemahaman masalah & relevansi (20%)** | Scope 4 kebutuhan krisis dikodekan di system prompt `app/Ai/Agents/CekarahAgent.php` (baris 31–47); 4 tool spesifik `app/Ai/Tools/*`; dataset bencana riil Sumatera 2025–2026 di `database/seeders/DatasetSeeder.php`. | Relevansi masalah hanya tampak dari data/scope; tidak ada halaman "latar belakang" lagi (halaman `/about` sudah dihapus). |
| **Kreativitas & inovasi solusi (20%)** | Routing intent otomatis via function-calling (bukan menu); **Radar Tren** clustering klaim mirip `app/Services/RadarService.php`; human-in-the-loop sync ke RAG `app/Http/Controllers/Portal/ClaimVerificationController.php@reindex`. | Radar saat ini minim data live (lihat §4c: 0 baris simulasi termuat). |
| **Pemanfaatan AI efektif & tepat guna (20%)** | (a) Agen utama Gemini `gemini-3-flash-preview` + 4 function tools; (b) RAG retrieval semantik `KnowledgeIndexer::similarChunks` & `ClaimIndexer::similar`; (c) riset web grounded Google Search `app/Ai/Agents/WebResearchAgent.php` (`gemini-2.5-flash`); (d) embeddings via `Laravel\Ai\Embeddings`. | Pencarian kemiripan kini **pgvector `<=>`** (vector(3072)); tanpa indeks ANN (dataset kecil) — perlu indeks bila data membesar. |
| **Responsible AI: risiko, mitigasi, peran manusia (15%)** | System prompt mewajibkan sumber + larangan vonis biner + fallback jujur (CekarahAgent baris 57–65); review queue + audit trail (`created_by/updated_by`) di Portal Relawan; deteksi fallback `MessageController::toolFoundNothing`; **guard privasi NIK + disclaimer medis/hukum + deteksi eskalasi** di `SafetyGuard` (diperbaiki). | — gap utama sudah ditutup (lihat §5). |
| **Fungsionalitas aplikasi/prototipe (15%)** | 28 route aktif (`php artisan route:list`); chat streaming SSE `MessageController::stream`; CRUD portal lengkap; peta Leaflet berfungsi; tes `tests/Feature/Portal/RadarTest.php` (5 lulus). | Beberapa skenario uji (`database/data/evaluation_cases.php`) masih merujuk Binjai yang datanya sudah diganti. |
| **Kejelasan presentasi — pitch/video/dokumentasi (10%)** | Dokumen konteks `context-update-proposal-fitur-baru.md`, `context-update-video-remotion-fitur-baru.md`; UI rapi (Landing, Chat, Portal). | **Tidak ada project Remotion di repo ini** — video di repo terpisah (lihat §6). |

---

## 3. Daftar Seluruh Halaman/Route Aplikasi

> Total 28 route (`route:list --except-vendor`). Halaman React: 12 komponen di `resources/js/pages/`. Route auth (login/register) diregistrasi Fortify.

### Landing — `/` (`home`)
- **Tujuan halaman**: Halaman depan publik; positioning produk + CTA ke chat.
- **Skenario terkait**: Lainnya (marketing/entry).
- **Target pengguna utama**: Masyarakat Umum.
- **Cara kerja**: 1. Render statis `Inertia::render('Landing')` (closure `routes/web.php:13`). 2. Menampilkan tagline + ticker statistik (hardcoded di komponen). 3. Tombol "Buka Chat" → `/chat`.
- **Komponen AI**: Tidak ada.
- **Data yang ditampilkan**: Statis di `resources/js/pages/Landing.tsx`. Sumber: `[SUMBER TIDAK TERIDENTIFIKASI — angka ticker hardcoded di komponen, PERLU DICEK MANUAL bila ingin diklaim sebagai data resmi]`.
- **Elemen visual untuk video**: Hero + BrandMark, ticker angka bencana.

### Chat Publik — `/chat` (`chat`)
- **Tujuan halaman**: Antarmuka tanya-jawab utama untuk warga, tanpa login.
- **Skenario terkait**: 1 (Info Bencana), 2 (Posko), 3 (Bansos), 4 (Verifikasi Hoaks) — keempatnya lewat satu kotak.
- **Target pengguna utama**: Warga Terdampak & Keluarga.
- **Cara kerja (input → proses → output)**:
  1. Frontend membuat sesi: `POST /api/chat-sessions` → token (`ChatSessionController@store`).
  2. Pesan dikirim streaming: `POST /api/chat-sessions/{token}/messages/stream` (`MessageController@stream`), via hook `resources/js/hooks/useChat.ts`.
  3. `CekarahAgent` klasifikasi intent → panggil 1 dari 4 tool → balasan token-by-token (SSE) + metadata (intent, confidence, sumber, lokasi peta).
- **Komponen AI**: `app/Ai/Agents/CekarahAgent.php` (model `gemini-3-flash-preview`, MaxSteps 6, Timeout 60); 4 tool `app/Ai/Tools/*`; riset web `WebResearchAgent` (`gemini-2.5-flash`, Google Search grounding). System prompt: scope 4 kebutuhan + aturan grounding (ringkas; file di atas).
- **Data yang ditampilkan**: `disaster_events`, `shelter_locations`, `aid_programs`, `claim_verifications`, `knowledge_chunks` (semua di-query oleh tool).
  - Sumber data: di-seed oleh `database/seeders/DatasetSeeder.php` (tabel terstruktur) & `database/seeders/KnowledgeSeeder.php` (knowledge), relasi sumber lewat tabel `sources`+`citations`.
  - Contoh `source_url` unik (tabel `sources`): `https://www.bnpb.go.id/berita/pasca-bencana-di-sumatra-...`, `https://www.komdigi.go.id/berita/berita-hoaks/detail/hoaks-air-laut-naik-di-wilayah-kabupaten-pidie-jaya`, `https://cekbansos.kemensos.go.id/`.
- **Elemen visual untuk video**: efek streaming (mengetik), indikator status tool ("Mencari lokasi posko…"), **peta Leaflet dengan marker**, **chip sumber yang bisa diklik**, badge keyakinan.

### Portal — Dashboard `/portal` (`portal.dashboard`)
- **Tujuan halaman**: Ringkasan jumlah data + 5 pertanyaan terakhir yang perlu ditinjau.
- **Skenario terkait**: Lainnya (operasional internal).
- **Target pengguna utama**: Relawan & Organisasi / Internal-Admin.
- **Cara kerja**: 1. Middleware `auth` + `role:admin,volunteer`. 2. `DashboardController@index` menghitung `count()` 3 tabel + `intent_logs.needs_review`. 3. Render `portal/Dashboard`.
- **Komponen AI**: Tidak ada (query agregat biasa).
- **Data yang ditampilkan**: `shelter_locations`, `aid_programs`, `claim_verifications`, `intent_logs`. Sumber: seeder + input relawan.
- **Elemen visual untuk video**: kartu statistik, daftar "perlu ditinjau".

### Portal — Radar Tren `/portal/radar` (`portal.radar.index`)
- **Tujuan halaman**: Mendeteksi lonjakan klaim hoaks sejenis & kebutuhan per wilayah dari log chat.
- **Skenario terkait**: Lintas-skenario (insight kolektif atas 2 & 4).
- **Target pengguna utama**: Relawan & Organisasi.
- **Cara kerja**: 1. `RadarController@index` baca filter (7/14/30 hari, sumber all/live/simulated). 2. `RadarService` cluster klaim (token Jaccard) + grup kebutuhan per `region`, hitung deret harian + flag lonjakan. 3. Render `portal/Radar` (bar chart SVG + badge "Perlu perhatian").
- **Komponen AI**: Tidak murni LLM — clustering memakai kemiripan token (bukan embedding). `region` diekstrak keyword di `app/Services/RegionExtractor.php`. *(Catatan: ini logika algoritmik, bukan AI generatif.)*
- **Data yang ditampilkan**: `intent_logs` (kolom `detected_intent`, `region`, `is_simulated`, `created_at`).
  - Sumber: dicatat otomatis tiap chat oleh `MessageController::logIntent`; data demo dari `database/seeders/RadarSimulationSeeder.php`.
  - ⚠️ **Kondisi aktual DB**: `intent_logs` = **8 baris, 0 di antaranya simulasi** — RadarSimulationSeeder belum/ tidak termuat saat ini, jadi grafik akan nyaris kosong sampai di-seed ulang (`php artisan db:seed --class=RadarSimulationSeeder`).
- **Elemen visual untuk video**: bar chart lonjakan + badge merah "Perlu perhatian", toggle "data simulasi".

### Portal — Perlu Ditinjau `/portal/review` (`portal.review.index`, `…resolve`)
- **Tujuan halaman**: Antrean jawaban "belum ada data resmi" untuk ditindaklanjuti relawan.
- **Skenario terkait**: Lintas 1–4 (human-in-the-loop).
- **Target pengguna utama**: Relawan & Organisasi.
- **Cara kerja**: 1. `ReviewController@index` ambil `intent_logs` `needs_review = true` (paginate). 2. Tombol "Tambah data resmi" → form terkait dengan pertanyaan prefilled. 3. `resolve` set `needs_review = false`.
- **Komponen AI**: Tidak ada (flag di-set oleh tool saat fallback).
- **Data yang ditampilkan**: `intent_logs`. Sumber: log chat warga.
- **Elemen visual untuk video**: daftar antrean + tombol "Tambah data resmi" (bukti peran manusia).

### Portal — Posko (CRUD) `/portal/shelters` (`index/create/store/edit/update/destroy`)
- **Tujuan halaman**: Kurasi data posko/shelter termasuk koordinat.
- **Skenario terkait**: 2 (Info Posko).
- **Target pengguna utama**: Relawan & Organisasi.
- **Cara kerja**: 1. `ShelterLocationController` CRUD. 2. Input koordinat via `resources/js/components/portal/CoordinatePicker.tsx`. 3. Tersimpan ke `shelter_locations` (audit `created_by/updated_by`).
- **Komponen AI**: Tidak langsung; data ini menjadi konteks tool `find_shelter_locations`.
- **Data yang ditampilkan**: `shelter_locations` (10 baris). Sumber: `DatasetSeeder` (Portal Satu Data BNPB) + input relawan.
  - Contoh `source_url`: `https://data.bnpb.go.id/dataset/.../titik-terdampak-dan-posko-pengungsian-bansor-sumatera-2025.xlsx`.
- **Elemen visual untuk video**: form + peta mini pemilih koordinat.

### Portal — Bantuan (CRUD) `/portal/aid`
- **Tujuan**: Kurasi program bantuan sosial. **Skenario**: 3 (Bansos). **Target**: Relawan & Organisasi.
- **Cara kerja**: `AidProgramController` CRUD → `aid_programs` (5 baris).
- **Komponen AI**: Tidak langsung; konteks tool `get_aid_assistance_info`.
- **Data**: `aid_programs`. Sumber: `DatasetSeeder` (Kemensos/BNPB/Kemendagri). Contoh `source_url`: `https://cekbansos.kemensos.go.id/`, `https://mediaindonesia.com/nusantara/867085/...dth...`.
- **Elemen visual**: tabel program + form.

### Portal — Klaim (CRUD) `/portal/claims`
- **Tujuan**: Kurasi hasil cek fakta manual + **sinkron otomatis ke RAG**. **Skenario**: 4 (Verifikasi Hoaks). **Target**: Relawan & Organisasi.
- **Cara kerja**: 1. `ClaimVerificationController@store/update`. 2. Sumber wajib (nama+URL+tanggal). 3. `reindex()` → `ClaimIndexer::index` membuat embedding → langsung dipakai tool `verify_claim`.
- **Komponen AI**: `ClaimIndexer` (embeddings Gemini) saat simpan.
- **Data**: `claim_verifications` (3 baris) + `sources`. Contoh `source_url`: `https://www.komdigi.go.id/berita/berita-hoaks/detail/hoaks-air-laut-naik-bak-tsunami-sapu-pantai-utara-jawa-tengah`.
- **Elemen visual**: form klaim + field sumber wajib (bukti grounding manusia).

### Auth — `/login`, `/register` (Fortify, `resources/js/pages/auth/*`)
- **Tujuan**: Autentikasi relawan/admin. **Skenario**: Lainnya (gerbang Portal). **Target**: Internal-Admin / Relawan.
- **Cara kerja**: Fortify standar; role default `volunteer` (migrasi `add_role_to_users_table`).
- **Komponen AI**: Tidak ada. **Data**: `users`. **Visual**: tidak untuk pitch.

### API (tanpa UI) — `routes/api.php`
- `POST /api/chat-sessions` (`ChatSessionController@store`) — buat sesi anonim (token).
- `POST /api/chat-sessions/{token}/messages` (`@store`) — kirim pesan (non-stream).
- `POST /api/chat-sessions/{token}/messages/stream` (`@stream`) — **endpoint utama chat (SSE streaming)**.
- `GET /api/chat-sessions/{token}/messages` (`@index`) — metadata sesi.
- Throttle `20,1`. Komponen AI: seluruh pipeline `CekarahAgent`.

---

## 4. Disclosure Tools, Library, Model, dan Data

### a) Tools & Library

| Nama | Versi | Fungsi dalam aplikasi (file/fitur) | Lisensi |
|---|---|---|---|
| laravel/framework | ^13.7 | Kerangka aplikasi (routing, Eloquent, queue) | MIT |
| laravel/ai (Laravel AI SDK) | (terpasang via `Laravel\Ai\*`) | Agen Gemini, tool-calling, embeddings — `app/Ai/**`, `app/Services/*Indexer.php` | MIT |
| laravel/fortify | ^1.37.2 | Auth Portal Relawan (login/register) | MIT |
| inertiajs/inertia-laravel | ^3.0 | Jembatan server→React (semua halaman) | MIT |
| laravel/wayfinder | ^0.1.14 | Route typed untuk frontend | MIT |
| laravel/tinker | ^3.0 | REPL debugging | MIT |
| @inertiajs/react | ^3.0.0 | SPA client (`resources/js/pages/**`) | MIT |
| react / react-dom | ^19.2.0 | UI seluruh halaman | MIT |
| typescript | ^5.7.2 | Tipe frontend | Apache-2.0 |
| tailwindcss | ^4.0.0 | Styling seluruh UI | MIT |
| vite | ^8.0.0 | Bundler/build | MIT |
| Leaflet (CDN) | 1.9.4 | Peta posko `components/ShelterMap.tsx` & `CoordinatePicker.tsx` | BSD-2-Clause |
| @radix-ui/* | 1.x | Komponen UI (dialog, select, dll.) | MIT |
| lucide-react | ^0.475.0 | Ikon | ISC |
| sonner | ^2.0.0 | Notifikasi toast | MIT |
| larastan/larastan | ^3.9 | Analisis statis (dev) | MIT |
| laravel/pint | ^1.27 | Formatter (dev) | MIT |
| phpunit/phpunit | ^12.5.23 | Testing (dev) | MIT |

### b) Model AI

| Peran | Model | Dipanggil di | Endpoint |
|---|---|---|---|
| Agen utama (chat + tool-calling) | `gemini-3-flash-preview` | `app/Ai/Agents/CekarahAgent.php` (`#[Model]`) → halaman `/chat` (API stream/store) | Gemini API (`config/ai.php` provider `gemini`, `GEMINI_API_KEY`, base `https://generativelanguage.googleapis.com/v1beta/`) |
| Riset web grounded | `gemini-2.5-flash` | `app/Ai/Agents/WebResearchAgent.php` (Google Search grounding) — dipanggil dari dalam 4 tool | Gemini API (sama) |
| Embeddings (RAG) | Provider `gemini` (default embedding SDK — **model spesifik tidak di-pin di kode** `[SUMBER TIDAK TERIDENTIFIKASI — default Laravel AI SDK]`) | `KnowledgeIndexer`, `ClaimIndexer`, `DatasetSeeder` via `Laravel\Ai\Embeddings` | Gemini API (sama) |

> Provider aktif = Gemini (`config/ai.php` → `default => env('AI_PROVIDER','gemini')`). Provider lain (anthropic/azure/openai/dll.) ada di config bawaan SDK tapi **tidak dipakai** di kode Cekarah.

### c) Sumber Data (jumlah baris diverifikasi via query DB aktual)

| Tabel | Baris (aktual) | Rentang tanggal | `source_url` unik (contoh) |
|---|---|---|---|
| `disaster_events` | **3** | `started_at` 2025-11-24 … 2025-11-25 | (rujukan via `sources`) |
| `shelter_locations` | **10** | — (tanggal via `sources`) | `data.bnpb.go.id/.../...bansor-sumatera-2025.xlsx` |
| `aid_programs` | **5** | — (tanggal via `sources`) | `cekbansos.kemensos.go.id`, `mediaindonesia.com/.../dth...` |
| `claim_verifications` | **3** | — (tanggal via `sources`) | `komdigi.go.id/.../hoaks-air-laut-naik-...-pidie-jaya` |
| `sources` | **13** | `published_at` 2025-12-01 … 2026-06-25 | BNPB, Komdigi, Kemensos, Kemenko PMK, Kemendagri |
| `knowledge_documents` | **10** (semua RIIL) | `source_date` 2025-12-02 … 2026-06-25 | `bnpb.go.id`, `data.bnpb.go.id`, `kemenkopmk.go.id`, kompas/Kemendagri |
| `knowledge_chunks` | **10** | — | turunan embedding `vector(3072)` dari knowledge_documents |
| `emergency_contacts` | **8** | — | BNPB 117, Basarnas 115, Ambulans 118/119, Damkar 113, Polisi 110, PMI, Kemensos 1500771, BMKG |
| `intent_logs` | **197 simulasi** + log chat live | — | demo radar sudah ter-seed (`RadarSimulationSeeder`) |

> ✅ **Diperbaiki 2026-06-25:**
> - **Dokumen sintetis dihapus.** Seluruh 20 dokumen "Data Sintetis Tim Cekarah" (`sintetis://`) telah dibuang; `knowledge_documents` kini **10 dokumen, semuanya bersumber resmi tertelusur**. Tidak ada lagi konten karangan di knowledge base.
> - **Tabel `emergency_contacts` kini ADA** (`2026_06_25_041000_create_emergency_contacts_table` + `EmergencyContactSeeder`, 8 baris). Kontak darurat tidak lagi sekadar hardcoded — `MessageController` & `SafetyGuard::escalationContacts` membacanya dari DB (dengan fallback).
>
> ℹ️ Catatan penamaan tetap berlaku: skema kode memakai `disaster_events`, `aid_programs`, `claim_verifications` (bukan `disasters`/`social_aids`/`hoax_verifications` dari dokumen sumber).

---

## 5. Implementasi Responsible AI yang Ditemukan di Kode

| Aspek | Status | Bukti / Catatan |
|---|---|---|
| Grounding wajib sumber+tanggal di tiap respons | `[ADA]` | `CekarahAgent` instructions baris 59–60 ("SELALU sertakan rujukan sumber + tanggal"); tool mengembalikan `references` dari `sources` (`app/Ai/Support/ToolReferences.php`). |
| Fallback "belum ada data resmi" saat RAG kosong | `[ADA]` | Tiap tool mengembalikan `found=false`/`no_official_data` saat DB & web kosong (`SearchDisasterInfoTool`, `VerifyClaimTool`, dll.); ditangkap `MessageController::toolFoundNothing` → flag `needs_review`. |
| Larangan AI berspekulasi di luar dokumen | `[ADA]` | System prompt baris 31–47 (scope 4 kebutuhan, tolak out-of-scope, "jangan menjawab dari pengetahuan umum"). |
| Eskalasi kontak darurat untuk situasi mengancam nyawa | `[ADA]` (diperbaiki) | `app/Ai/Support/SafetyGuard.php::isLifeThreatening` mendeteksi keyword bahaya (terjebak, hanyut, tenggelam, dll.); `MessageController::applyEscalation` memaksa `escalation_suggested=true` + lampirkan kontak dari DB, terlepas dari klasifikasi model. Kontak dari `EmergencyContact` (fallback hardcoded). |
| Larangan klaim medis/hukum definitif | `[ADA]` (diperbaiki) | Ditambahkan ke system prompt `CekarahAgent`: larangan diagnosis/nasihat medis & hukum definitif; arahkan ke ambulans 119 (medis) / instansi berwenang (hukum). |
| Penanganan privasi (cegah terima/simpan NIK) | `[ADA]` (diperbaiki) | `SafetyGuard::redactSensitive` meredaksi pola NIK/KK (16 digit) **sebelum** pesan dikirim ke model & sebelum disimpan di `intent_logs` (`MessageController::store/stream`). System prompt juga melarang meminta NIK. Diuji di `tests/Feature/SafetyGuardTest.php`. |
| Vonis non-biner pada verifikasi klaim | `[ADA]` | Prompt baris 63 + `VerifyClaimTool` guidance ("jangan vonis tanpa rujukan"). |
| Auditabilitas | `[ADA]` | `intent_logs` mencatat intent/tool/region tiap pesan; audit trail `created_by/updated_by` pada data portal. |
| Transparansi data simulasi | `[SEBAGIAN]` | `Source.is_simulated` & `intent_logs.is_simulated` ada; tetapi dataset utama kini `is_simulated=false`. Yang masih sintetis = 20 knowledge_documents (ditandai di `source_name`). |

---

## 6. Bahan Mentah untuk Naskah Video Pitch (Remotion)

> ⚠️ **Tidak ada project Remotion di repo ini** (hanya dokumen konteks). Naskah di bawah untuk dieksekusi di repo Remotion terpisah.

**Scene Hook / Masalah** (angka dari data riil `knowledge_documents` + `disaster_events`):
- "1.189 jiwa meninggal" akibat banjir-longsor Sumatera (Aceh 550, Sumut 375, Sumbar 231) — sumber BNPB per 12 Jan 2026.
- "195.542 pengungsi", terbanyak di Aceh Utara (67.876 jiwa).
- Hoaks "air laut naik Pidie Jaya" memicu kepanikan massal & 2 lansia cedera — sumber Komdigi.

**Scene Solusi** (positioning, 1 kalimat):
- "Cekarah — satu kotak chat yang otomatis mengenali kebutuhanmu di 48 jam pertama krisis: info bencana, posko, bantuan, dan cek hoaks, selalu dengan sumber resmi."

**Scene Demo 1 — Info Bencana + Posko (gabungan):**
- Pertanyaan: "Bagaimana situasi banjir di Aceh, dan posko di Aceh Tamiang di mana?"
- Rekam: efek streaming + indikator status tool → **peta Leaflet dengan marker** → chip sumber BNPB yang bisa diklik.

**Scene Demo 2 — Bansos:**
- Pertanyaan: "Saya korban bencana, bagaimana cara cek bansos PKH/BPNT dan apa itu DTH?"
- Rekam: daftar program + penyedia (Kemensos/BNPB) + chip sumber `cekbansos.kemensos.go.id`; tonjolkan sistem **tidak meminta NIK** (arahkan ke situs resmi).

**Scene Demo 3 — Verifikasi Hoaks:**
- Pertanyaan: "Benarkah air laut naik di Pidie Jaya dan akan tsunami?"
- Rekam: jawaban non-biner + penjelasan + chip sumber Komdigi; tampilkan badge status.

**Scene Responsible AI** (bukti dari kode, bukan klaim):
- Rekam alur: chat menjawab **"belum ada data resmi"** (fallback) → muncul di **Review Queue** `/portal/review` → relawan menambah data + sumber wajib → chat ditanya ulang → jawaban berubah dengan sumber baru. Membuktikan human-in-the-loop nyata.

**Scene Closing:**
- Ringkasan: "AI yang jujur, ber-sumber, dan diawasi relawan — navigator awal warga di 48 jam pertama." CTA: buka Cekarah.

---

## Lampiran — Status Perbaikan Gap (diperbarui 2026-06-25)

| # | Gap | Status |
|---|---|---|
| 1 | pgvector tidak aktif | ✅ **Selesai** — kolom `vector(3072)` + operator `<=>` (migrasi + 2 service + cast). |
| 2 | Tabel `emergency_contacts` tidak ada | ✅ **Selesai** — tabel + model + seeder (8 kontak) + wiring ke `SafetyGuard`/`MessageController`. |
| 3 | 20/30 knowledge doc sintetis | ✅ **Selesai** — dokumen sintetis dihapus; tersisa 10 dokumen riil. |
| 4 | Guard NIK / disclaimer medis-hukum / eskalasi | ✅ **Selesai** — `SafetyGuard` (redaksi NIK + deteksi eskalasi) + aturan prompt; diuji `SafetyGuardTest`. |
| 5 | Radar/region = algoritmik (bukan AI) | ℹ️ **Klarifikasi dokumentasi** — sudah ditandai jujur; tetap algoritmik (token Jaccard + keyword), bukan AI generatif. |
| 6 | Nama model | ℹ️ **Informasional** — `gemini-3-flash-preview` (agen), `gemini-2.5-flash` (riset web), embeddings Gemini 3072-dim. |
| 7 | Radar kosong & eval cases Binjai | ✅ **Selesai** — `RadarSimulationSeeder` (197 baris, wilayah riil) + `evaluation_cases.php` diselaraskan ke data riil (Aceh/Sumut/Sumbar). |

**Catatan tes:** Tes baru `SafetyGuardTest` (4) & `RadarTest` (5) lulus. Kegagalan suite lain (Teams/Dashboard "Route not defined", auth "Vite manifest") **pra-eksis & tidak terkait** perubahan ini — tes sisa starter-kit untuk fitur yang sudah dihapus + halaman yang butuh `npm run build`.
