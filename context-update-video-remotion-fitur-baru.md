# Konteks Update Video Remotion — Fitur Baru Cekarah

> Untuk diberikan ke **Claude Code (sesi terpisah)** guna memperbarui video pitch/demo Remotion dengan tiga fitur baru. Deskriptif per scene — **bukan kode komposisi**. Durasi target tetap **2–5 menit** (Tahap 1 Babak Seleksi Daring).

---

## 0. Hasil Audit Project Remotion

⚠️ **Tidak ditemukan project Remotion di repository aplikasi Cekarah** (`c:\laragon\www\cekarah-ai`) — tidak ada dependensi `remotion` di `package.json` maupun folder Remotion. Video berada di **repo/lokasi terpisah**.

**Wajib dilakukan lebih dulu oleh sesi berikutnya:** arahkan Claude Code ke lokasi project Remotion yang sebenarnya, lalu audit nyata: daftar `Composition`/`Root.tsx`, urutan scene & durasi, script narasi terbaru, dan aset (rekaman layar, logo, audio). Penanda KEEP/UPDATE/REMOVE di Bagian 2 adalah **kerangka** untuk diterapkan setelah audit nyata itu.

---

## 1. Scene Baru yang Sebaiknya Ditambahkan (3 Fitur)

Fokus menampilkan **nilai baru**, bukan sekadar "fitur ada".

**A. Demo Human-in-the-Loop (end-to-end) — scene paling kuat untuk Responsible AI.**
Tampilkan satu alur utuh:
1. Warga bertanya di chat → Cekarah menjawab *"belum ada data resmi"* (jujur, tidak mengarang).
2. Potong ke Portal Relawan → pertanyaan itu muncul di antrean **"Perlu Ditinjau"**.
3. Relawan klik **"Tambahkan data resmi"** → mengisi form klaim + sumber wajib.
4. Kembali ke chat warga → pertanyaan sama ditanya ulang → **jawaban kini berubah**, lengkap dengan sumber yang baru ditambahkan relawan.
Narasi kunci: *"AI tahu batasnya, manusia melengkapinya — dan sistem langsung membaik."* Ini bukti visual Responsible AI yang sulit dibantah.

**B. Radar Tren Hoaks & Kebutuhan — Cekarah sebagai sistem deteksi dini.**
Tampilkan dashboard Radar: grafik lonjakan **klaim hoaks "bendungan jebol"** dan **wilayah "Binjai" dengan lonjakan kebutuhan**, dengan badge **"Perlu perhatian"**.
Narasi kunci: *"Bukan sekadar menjawab satu per satu — Cekarah membaca pola, mendeteksi hoaks dan kebutuhan yang sedang naik."* Tegaskan framing jujur: sinyal untuk ditindaklanjuti manusia, data ditandai simulasi untuk demo.

**C. Bukti Kredibilitas — angka Skrip Evaluasi (overlay/slide ringkas).**
Tampilkan angka sebagai overlay (bukan rekaman proses run): **Intent 100% · Sitasi 100% · Tolak out-of-scope 100%**.
Narasi kunci: *"Klaim kami terukur, bukan sekadar janji."* Cukup 3–5 detik sebagai penegas sebelum penutup.

---

## 2. Penanda Scene Lama (KEEP / UPDATE / REMOVE)

Terapkan setelah audit project Remotion nyata.

| Jenis scene lama | Rekomendasi | Alasan |
|---|---|---|
| Hook masalah (data bencana & hoaks) | **KEEP** | Premis tetap kuat. |
| Demo chat dasar (info/verifikasi/posko/bansos) | **UPDATE** | Rekam ulang agar menampilkan streaming + sumber yang bisa diklik + data web terkini. |
| Penjelasan "navigasi & verifikasi" versi lama | **UPDATE** | Pertegas sebagai 5 kategori + tool-calling otomatis. |
| Scene peta posko | **KEEP** (rekam ulang jika perlu) | Tetap visual kuat; pastikan pakai Leaflet/peta terbaru. |
| Segmen Responsible AI naratif lama | **UPDATE/GANTI** | Ganti dengan **demo human-in-the-loop (Scene A)** yang konkret. |
| (tidak ada) Radar / deteksi dini | **TAMBAH BARU (Scene B)** | Kapabilitas baru paling membedakan. |
| (tidak ada) Bukti metrik | **TAMBAH BARU (Scene C)** | Kredibilitas terukur. |
| Penutup/CTA | **KEEP** (samakan identitas visual navy+merah) | — |

---

## 3. Usulan Storyboard Baru (target 2–5 menit)

| # | Scene | Yang ditampilkan | Poin narasi kunci |
|---|---|---|---|
| 1 | Hook | Angka korban & hoaks (animasi), nuansa navy gelap | "48 jam pertama krisis: warga bingung cari bantuan & sulit bedakan benar/hoaks." |
| 2 | Solusi | Logo + satu kotak chat | "Cekarah: satu chat, mengenali kebutuhan otomatis, menjawab dari sumber resmi." |
| 3 | Demo inti (ringkas) | Tanya posko Binjai → jawaban streaming + **peta** + sumber diklik | "Jawaban konkret, di peta, dengan sumber resmi — real-time." |
| 4 | **Human-in-the-loop** (Scene A) | Chat "belum ada data" → review queue → relawan isi data → chat membaik | "AI tahu batasnya; manusia melengkapinya; sistem langsung membaik." (inti Responsible AI) |
| 5 | **Radar Tren** (Scene B) | Dashboard lonjakan hoaks "bendungan jebol" + wilayah Binjai, badge "Perlu perhatian" | "Cekarah membaca pola — deteksi dini hoaks & kebutuhan, sinyal untuk petugas." |
| 6 | **Bukti metrik** (Scene C) | Overlay: Intent 100% · Sitasi 100% · Tolak out-of-scope 100% | "Terukur, bukan sekadar janji." |
| 7 | Penolakan out-of-scope (opsional, singkat) | Pertanyaan non-bencana ditolak sopan | "Fokus & aman — di luar topik diarahkan kembali." |
| 8 | Penutup/CTA | Tagline + identitas visual | "Cekarah — navigator awal warga, diperkuat relawan." |

Catatan durasi: Scene 4 (human-in-the-loop) dan 5 (Radar) adalah **momen puncak** — beri porsi terbesar. Scene 6 cukup 3–5 detik. Jaga total ≤ 5 menit; jika ketat, gabungkan Scene 3 & 7.

---

## 4. Catatan Teknis untuk Eksekusi (sesi Remotion berikutnya)

- **Rekam ulang dari aplikasi terbaru** agar menampilkan: streaming chat, peta (Leaflet, tanpa API key), sumber diklik, Portal Relawan (login `test@example.com`), antrean tinjauan, dan Radar (jalankan `php artisan db:seed --class=TrendLogSeeder` lebih dulu agar grafik berisi).
- **Identitas visual konsisten:** navy `#0A0F1E`, aksen merah `#E63946`, biru interaktif `#3B82F6`.
- **Etika tampilan:** saat menampilkan Radar, pertahankan label "perlu perhatian / data simulasi" agar tidak terkesan mengklaim data riil.
- **Sebelum mengubah apa pun:** audit struktur scene project Remotion nyata (Bagian 0), petakan ke KEEP/UPDATE/REMOVE (Bagian 2), lalu eksekusi storyboard (Bagian 3).
