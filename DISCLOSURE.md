# Disclosure — Cekarah AI

Platform Asisten Krisis & Verifikasi Informasi
LKS Dikmen Tingkat Nasional 2026 — Ekshibisi Kecerdasan Artifisial

---

## Model & Provider AI

| Kemampuan | Model | Provider |
|-----------|-------|----------|
| Text generation (agent) | gemini-2.5-flash | Google Gemini API |
| Embeddings (RAG) | text-embedding-004 | Google Gemini API |

Catatan: pgvector tidak tersedia di lingkungan pengembangan (Laragon Windows).
Sebagai gantinya, similarity search dilakukan via cosine similarity di PHP menggunakan
embedding yang disimpan dalam kolom JSONB PostgreSQL.

---

## Framework & Paket Utama

| Paket | Versi |
|-------|-------|
| PHP | 8.5 |
| Laravel | 13.16.1 |
| laravel/ai | v0.8.1 |
| Inertia.js (Laravel) | v3 |
| @inertiajs/react | ^3.0.0 |
| React | ^19.2.0 |
| Tailwind CSS | v4 |
| PostgreSQL | 14.5 |

---

## Sumber Data Knowledge Base

Seluruh data bersifat **sintetis** — dibuat oleh tim Cekarah berdasarkan informasi
publik dari lembaga resmi. Tidak ada data pribadi, data korban, atau data sensitif
yang digunakan.

| Nama Sumber | URL Referensi | Kategori | Jenis Data |
|-------------|--------------|----------|------------|
| BNPB (Badan Nasional Penanggulangan Bencana) | bnpb.go.id | Prosedur Evakuasi | Sintetis (berdasarkan SOP BNPB) |
| Basarnas (Badan Nasional Pencarian dan Pertolongan) | basarnas.go.id | Prosedur SAR | Sintetis (berdasarkan SOP Basarnas) |
| Kementerian Sosial | kemensos.go.id | Bantuan Sosial | Sintetis (berdasarkan panduan Kemensos) |
| PMI (Palang Merah Indonesia) | pmi.or.id | Bantuan Darurat | Sintetis (berdasarkan data PMI) |
| BMKG (Badan Meteorologi, Klimatologi, dan Geofisika) | bmkg.go.id | Verifikasi Bencana | Sintetis (berdasarkan data BMKG) |
| Kemkomdigi — Aduan Hoaks | aduankonten.id | Verifikasi Hoaks | Sintetis (berdasarkan panduan resmi) |
| DTSEN 2026 / Cek Bansos | cekbansos.kemensos.go.id | Bantuan Sosial | Sintetis (berdasarkan panduan Kemensos) |

Dokumen knowledge base mencakup 20 topik:
- **Kelompok A (6 dok):** Prosedur evakuasi, dokumen evakuasi, posko BNPB, Basarnas,
  fasilitas pengungsian, direktori kontak darurat
- **Kelompok B (7 dok):** Cek Bansos, daftar offline, DTSEN 2026, status penerima,
  PKH, BPNT, usul sanggah desil
- **Kelompok C (7 dok):** Hoaks tsunami, hoaks gempa susulan, rekening donasi palsu,
  pengumuman resmi vs hoaks, saluran verifikasi, lapor aduankonten.id,
  kasus nyata Pidie Jaya Desember 2025

---

## Tools yang Digunakan Selama Development

- **Claude Code** (Anthropic) — Sonnet 4.6 — asisten pengembangan utama
- **Laragon** — lingkungan pengembangan lokal Windows
- **PostgreSQL 14.5** — database relasional

---

## Arsitektur Sistem

```
User → Chat UI (React + Inertia)
     → POST /api/chat-sessions/{token}/messages
     → MessageController
     → CekarahAgent (laravel/ai SDK)
          → ClassifyIntentTool (keyword matching)
          → SearchKnowledgeBaseTool (cosine similarity via KnowledgeIndexer)
          → CheckInformationFreshnessTool (cek source_date vs 6 bulan)
          → GetEscalationContactsTool (kontak petugas per intent)
     → JSON response (reply, intent, confidence, escalation_contacts, sources_used)
     → MessageBubble + ConfidenceBar + EscalationPanel + SourceCard
```

---

## Catatan Responsible AI

1. **Tidak ada data pribadi** — Cekarah tidak menyimpan identitas pengguna.
   Sesi diidentifikasi hanya dengan token acak 40 karakter.
2. **AI sebagai navigator awal** — Setiap respons menyertakan rujukan sumber resmi
   dan kontak petugas manusia. AI tidak pernah mengklaim sebagai otoritas final.
3. **Transparansi confidence** — Setiap respons menampilkan confidence score.
   Jika confidence < 0.6 atau AI menyarankan eskalasi, panel kontak darurat
   ditampilkan secara otomatis.
4. **Tidak vonis biner** — Sistem tidak pernah menyatakan "HOAKS" atau "FAKTA"
   tanpa penjelasan dan rujukan sumber.
5. **Fallback darurat** — Jika API AI gagal, sistem otomatis menampilkan
   kontak BNPB 117 ext 7 dan Basarnas 115.
6. **Data sintetis** — Seluruh knowledge base adalah data sintetis berbasis
   informasi publik, bukan data sensitif atau data korban.
