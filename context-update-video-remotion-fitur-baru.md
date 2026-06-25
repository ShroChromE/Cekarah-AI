# Konteks Update Video Remotion — Tiga Fitur Baru Cekarah

> **Untuk siapa:** dokumen ini akan diberikan **kembali ke Claude Code (sesi terpisah)** untuk memperbarui video demo/pitch Remotion. Ini melanjutkan [context-update-video-remotion.md](context-update-video-remotion.md) (storyboard 9-scene sebelumnya) dengan menambah **tiga fitur baru**: Portal Relawan, Skrip Evaluasi, dan Radar Tren.
>
> **Deskriptif per scene — bukan kode komposisi.** Implementasi kode Remotion diminta terpisah setelah storyboard ini di-review.

---

## 0. Hasil Audit Project Remotion (per 25 Juni 2026)

⚠️ **Masih tidak ditemukan project Remotion di dalam repo aplikasi Cekarah** (`d:\laragon-8-6-1\www\cekarah-ai-app`). Pencarian menyeluruh hanya menemukan **dua dokumen konteks** (`context-update-video-remotion.md` dan dokumen ini) — tidak ada dependensi `remotion`, folder `src/` komposisi, `Root.tsx`, atau aset video.

**Artinya tidak berubah dari audit sebelumnya:** project Remotion berada di **repo/folder terpisah**. Sesi Claude Code yang akan mengeksekusi update **harus diarahkan ke lokasi project Remotion sebenarnya**, lalu mengaudit nyata:
- Struktur `src/` & daftar `Composition` di `Root.tsx`
- Daftar scene/sequence + durasi (frame) masing-masing
- Script narasi (voiceover/teks on-screen) terkini
- Aset (logo, screen recording, audio) yang dipakai

Penanda KEEP/UPDATE/REMOVE di bagian 3 disusun sebagai **kerangka** untuk diterapkan setelah audit nyata tersebut.

---

## 1. Tiga Kapabilitas Baru yang Layak Masuk Video

Berbeda dari storyboard lama yang men-demo **5 kategori chat warga**, tiga fitur ini menunjukkan **nilai baru di luar tanya-jawab** — penting untuk membedakan Cekarah dari "sekadar chatbot":

1. **Human-in-the-loop (Portal Relawan)** — paling kuat secara visual untuk Responsible AI. Alur end-to-end: chat warga gagal menjawab → muncul di review queue relawan → relawan menambah data bersumber → chat warga ditanya ulang → **jawaban berubah membaik dengan sumber baru**. Membuktikan "AI memandu, manusia memutuskan" secara nyata.
2. **Radar Tren** — memposisikan Cekarah sebagai **sistem deteksi dini**, bukan chatbot pasif. Grafik lonjakan klaim hoaks & kebutuhan per wilayah + badge "Perlu perhatian".
3. **Skrip Evaluasi Otomatis** — **bukti kredibilitas berbasis angka**. Ditampilkan sebagai overlay angka/slide ringkas (% sitasi, akurasi intent, penolakan out-of-scope, latency) — **bukan** rekaman proses run script di terminal.

---

## 2. Scene Baru yang Sebaiknya Ditambahkan (alur menunjukkan NILAI, bukan sekadar "fitur ada")

### Scene Baru A — Human-in-the-Loop End-to-End *(scene andalan Responsible AI)*
**Tampilkan (satu alur utuh, ±25–35 detik):**
1. Sisi warga: ketik pertanyaan yang belum ada datanya (mis. *"Posko pengungsian di Langkat di mana?"*) → chat jujur menjawab **"belum ada data resmi"**.
2. Transisi ke Portal Relawan: pertanyaan itu **muncul di Review Queue** ("Perlu Ditinjau") dengan kategori intent.
3. Relawan klik **"Tambah data resmi"** → form ter-prefill → isi data + **sumber wajib** (nama, URL, tanggal) → simpan.
4. Sorot micro-copy: *"disinkronkan ke chat"* (data langsung masuk RAG).
5. Kembali ke sisi warga: pertanyaan sama ditanya ulang → **jawaban kini terisi**, lengkap dengan **chip sumber** baru.

**Poin narasi kunci:** *"Saat Cekarah belum punya jawaban, ia tidak mengarang — ia meminta manusia. Relawan menambahkan data resmi, dan jawaban untuk warga langsung membaik. Inilah AI yang jujur dan diawasi manusia."*

### Scene Baru B — Radar Tren (deteksi dini)
**Tampilkan (±15–20 detik):** dashboard Radar — grafik batang per-hari untuk **klaim hoaks yang sedang naik** (badge merah "Perlu perhatian") dan **wilayah dengan lonjakan kebutuhan** (mis. Binjai). Tunjukkan toggle **"data simulasi"** sekilas untuk kejujuran.

**Poin narasi kunci:** *"Dari ribuan percakapan, Cekarah mengenali pola: klaim hoaks yang mulai menyebar dan wilayah yang kebutuhannya melonjak. Bukan vonis pasti — melainkan sinyal dini untuk ditindaklanjuti relawan."*

### Scene Baru C — Bukti Terukur (Skrip Evaluasi)
**Tampilkan (±10–12 detik):** slide ringkas / overlay angka besar di atas latar navy — beberapa metrik kunci dari laporan evaluasi (mis. *"X% jawaban bersumber · Y% akurasi intent · Z% out-of-scope ditolak"*). **Isi angka dari laporan `cekarah:evaluate` terbaru**, bukan angka karangan.

**Poin narasi kunci:** *"Kami tidak hanya mengklaim. Setiap kemampuan diuji otomatis terhadap ratusan pertanyaan — dan inilah hasilnya."*

---

## 3. Penanda Scene Lama (KEEP / UPDATE / REMOVE)

Mengacu storyboard 9-scene di [context-update-video-remotion.md](context-update-video-remotion.md). Terapkan setelah audit project Remotion nyata.

| Scene lama | Rekomendasi | Alasan |
|---|---|---|
| 1. Hook (masalah bencana & hoaks) | **KEEP** | Premis tak berubah; pembuka kuat |
| 2. Perkenalan solusi (1 kotak chat) | **UPDATE ringan** | Tambah teaser bahwa ada sisi relawan + deteksi dini |
| 3–4. Demo kategori info & verifikasi | **KEEP** | Masih relevan; bisa dipadatkan agar ada ruang untuk scene baru |
| 5. Demo posko + PETA (puncak lama) | **KEEP** | Tetap visual terkuat untuk sisi warga |
| 6–7. Demo bansos & penolakan out-of-scope | **KEEP / padatkan** | Pertahankan, namun pertimbangkan ringkas agar durasi total terjaga |
| 8. Responsible AI (badge keyakinan, sumber, data sintetis) | **UPDATE besar** | Gabungkan/menyusul **Scene Baru A** (human-in-the-loop) sebagai bukti terkuat; sisipkan audit trail |
| 9. Penutup/CTA | **UPDATE teks** | Reframe Cekarah sebagai "navigator + deteksi dini + diawasi relawan" |
| (jika ada) mitra/jumlah pengguna/testimoni | **REMOVE** | Tetap tidak relevan; berisiko klaim berlebih |

---

## 4. Usulan Storyboard Baru Keseluruhan (durasi target 2–5 menit, ideal ~3 menit)

| # | Scene | Yang ditampilkan | Poin narasi kunci | Estimasi |
|---|---|---|---|---|
| 1 | Hook | Counter angka bencana & hoaks, nuansa navy | Dua masalah warga di 48 jam pertama | 15 dtk |
| 2 | Solusi | Logo + 1 kotak chat, teaser 3 sisi | Satu pintu: tanya, verifikasi, deteksi dini | 12 dtk |
| 3 | Demo info bencana | Chat streaming + chip sumber | Rangkuman dari sumber resmi | 15 dtk |
| 4 | Demo verifikasi klaim | Jawaban non-biner + rujukan BMKG | Bukan vonis sepihak | 15 dtk |
| 5 | Demo posko + PETA | Peta marker interaktif (puncak sisi warga) | Posko terdekat di peta, bersumber | 20 dtk |
| 6 | Demo bansos (padat) | Daftar program + sumber | Memandu menemukan bantuan | 10 dtk |
| 7 | **BARU A — Human-in-the-loop** | Alur warga→review queue→relawan isi→jawaban membaik | AI jujur & diawasi manusia | 30 dtk |
| 8 | **BARU B — Radar Tren** | Grafik lonjakan klaim & wilayah + badge "Perlu perhatian" | Sistem deteksi dini, framing jujur | 18 dtk |
| 9 | **BARU C — Bukti terukur** | Overlay metrik dari `cekarah:evaluate` | Diuji, bukan diklaim | 12 dtk |
| 10 | Penolakan out-of-scope (opsional/padat) | Penolakan sopan | Fokus & aman | 8 dtk |
| 11 | Penutup/CTA | Tagline + identitas visual navy/merah | Navigator awal + deteksi dini warga | 12 dtk |

**Total estimasi ±2 menit 49 detik** — aman di dalam ketentuan 2–5 menit (Tahap 1 Babak Seleksi Daring). Jika perlu dipangkas: padatkan Scene 6 & 10. Scene andalan baru (7) **jangan** dipangkas.

---

## 5. Catatan Teknis untuk Eksekusi Remotion (sesi berikutnya)

- **Rekaman layar baru yang perlu disiapkan:**
  - Alur human-in-the-loop **dua sisi** (chat warga + Portal Relawan) — rekam berurutan agar transisi mulus; gunakan akun relawan (`role = volunteer`).
  - Dashboard Radar dengan **data simulasi** sudah ter-seed: `php artisan db:seed --class=RadarSimulationSeeder` (197 baris `is_simulated`, aman & idempanten) agar grafik terlihat representatif.
  - Untuk overlay metrik: jalankan `php artisan cekarah:evaluate` lalu ambil angka dari `storage/app/evaluations/eval-<timestamp>.md`.
- **Kejujuran visual:** saat menampilkan Radar, biarkan label "data simulasi/demo" tetap terlihat (atau beri caption) — selaras prinsip Caveats; jangan mengesankan grafik = pengguna riil.
- **Konsistensi visual:** samakan palet UI terbaru — navy `#0A0F1E`, aksen merah `#E63946`, biru interaktif `#3B82F6`; badge "Perlu perhatian" memakai merah.
- **Urutan prioritas momen puncak:** (1) Scene 7 human-in-the-loop, (2) Scene 5 peta, (3) Scene 8 radar.
- **Sebelum mengubah apa pun:** audit dulu struktur scene project Remotion nyata (bagian 0), petakan ke KEEP/UPDATE/REMOVE (bagian 3), lalu terapkan storyboard (bagian 4).
