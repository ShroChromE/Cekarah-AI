# Konteks Update Video Remotion — Cekarah

> Dokumen ini untuk diberikan ke **Claude Code (sesi terpisah)** guna memperbarui video demo/pitch yang dibuat dengan Remotion. Berisi: hasil audit project Remotion, kapabilitas baru yang layak ditampilkan, penanda KEEP/UPDATE/REMOVE per scene, dan storyboard alur demo baru. **Deskriptif per scene — bukan kode komposisi Remotion** (implementasi kode diminta terpisah setelah storyboard di-review).

---

## 0. Hasil Audit Project Remotion

⚠️ **Tidak ditemukan project Remotion di repository aplikasi Cekarah ini** (`c:\laragon\www\cekarah-ai`). Pencarian menyeluruh (package.json, folder, file `.ts/.tsx/.json/.md`) tidak menemukan dependensi atau folder Remotion mana pun — satu-satunya kemunculan kata "remotion" ada di file instruksi prompt.

**Artinya:** video Remotion berada di **repo/folder terpisah**. Sebelum mengeksekusi update, sesi Claude Code berikutnya perlu **diarahkan ke lokasi project Remotion yang sebenarnya**, lalu melakukan audit nyata terhadap:
- Struktur `src/` & daftar komposisi (`Composition`/`Root.tsx`)
- Daftar scene/sequence dan durasinya
- Script narasi (voiceover/teks on-screen) yang ada
- Aset (gambar, logo, screen recording, audio) yang dipakai

Penanda KEEP/UPDATE/REMOVE di bagian 2 disusun sebagai **kerangka generik** untuk diterapkan setelah audit nyata tersebut.

---

## 1. Kapabilitas Baru yang Layak Ditampilkan di Video

Berdasarkan fitur yang **kini benar-benar berfungsi** (Fase 1–5):

1. **Streaming chat** — jawaban muncul bertahap (efek mengetik) + indikator status ("Mencari di knowledge base…", "Mencari lokasi posko…"). Memberi kesan responsif & hidup; bagus untuk B-roll layar.
2. **Routing intent otomatis** — sistem memilih sendiri kategori yang tepat tanpa user memilih menu. Layak ditonjolkan: satu kotak chat, lima perilaku berbeda.
3. **Peta posko interaktif** — marker + daftar lokasi yang bisa diklik untuk fokus ke marker (Leaflet). Visual paling "wah" untuk demo.
4. **Sumber yang bisa diklik** — tiap jawaban menampilkan rujukan resmi (BNPB/BMKG/dll) sebagai tautan. Memperkuat pesan Responsible AI/anti-hoaks.
5. **Penolakan di luar konteks** — sistem menolak sopan pertanyaan non-bencana. Bagus untuk menegaskan fokus & keamanan.
6. **Transparansi & grounding** — badge keyakinan, "belum ada data resmi" alih-alih mengarang, penanda data sintetis. Untuk segmen Responsible AI.

---

## 2. Penanda Scene (KEEP / UPDATE / REMOVE)

Terapkan setelah audit project Remotion nyata. Pedoman keputusan:

| Jenis scene lama | Rekomendasi | Alasan |
|------------------|-------------|--------|
| Hook/latar belakang masalah (data bencana & hoaks) | **KEEP** | Premis tidak berubah; tetap kuat sebagai pembuka |
| Penjelasan solusi versi lama ("navigasi & verifikasi" umum) | **UPDATE** | Ganti dengan narasi 5 kategori + routing otomatis |
| Demo chat lama (jika respons tampil sekaligus/blocking) | **UPDATE** | Rekam ulang dengan efek streaming + indikator status |
| Scene fitur peta (jika belum ada) | **TAMBAH BARU** | Peta posko interaktif kini tersedia & sangat visual |
| Klaim arsitektur teknis lama (RAG sederhana) | **UPDATE** | Pertajam ke tool-calling + retrieval terstruktur+semantik |
| Segmen Responsible AI (jika ada) | **UPDATE** | Tambah: grounding, sumber diklik, penolakan out-of-scope, data sintetis transparan |
| Segmen "mitra/jumlah pengguna/testimoni" (jika ada) | **REMOVE** | Tidak relevan untuk alat krisis; berisiko terkesan klaim berlebih |
| Penutup/CTA | **KEEP** (sesuaikan teks) | Pertahankan, samakan dengan identitas visual terbaru (navy + merah) |

---

## 3. Usulan Storyboard Alur Demo Baru

Mengikuti 5 skenario E2E yang sudah berhasil dijalankan. Setiap scene: **apa yang ditampilkan → poin narasi kunci.**

**Scene 1 — Hook (masalah).**
Tampilkan: angka bencana & hoaks (animasi counter), nuansa navy gelap.
Narasi: "Dalam 48 jam pertama krisis, warga menghadapi dua masalah sekaligus — tak tahu ke mana mencari bantuan, dan sulit membedakan kabar benar dari hoaks."

**Scene 2 — Perkenalan solusi.**
Tampilkan: logo Cekarah + satu kotak chat.
Narasi: "Cekarah: satu kotak chat, yang otomatis mengenali kebutuhanmu dan menjawab dari sumber resmi."

**Scene 3 — Demo Kategori 1 (informasi bencana).**
Tampilkan: ketik "Banjir sedang terjadi di mana saja?" → jawaban streaming + chip sumber (BNPB/BMKG).
Narasi: "Tanya situasi terkini — Cekarah merangkum dari data resmi, lengkap dengan sumber dan tanggalnya."

**Scene 4 — Demo Kategori 2 (verifikasi klaim).**
Tampilkan: ketik klaim "akan ada banjir besar hari ini, benar?" → jawaban non-biner + rujukan BMKG.
Narasi: "Dapat kabar meresahkan? Cekarah menjelaskan dengan alasan dan rujukan — bukan vonis sepihak."

**Scene 5 — Demo Kategori 3 (lokasi posko + PETA).** *(scene puncak)*
Tampilkan: ketik "Posko pengungsian di Binjai?" → **peta muncul** dengan marker, klik daftar lokasi → marker fokus + info window.
Narasi: "Butuh tempat mengungsi? Cekarah menunjukkan posko terdekat langsung di peta, dengan alamat dan sumber resmi."

**Scene 6 — Demo Kategori 4 (bantuan sosial).**
Tampilkan: ketik "Daerah saya di Binjai kena bencana, ada bantuan apa?" → daftar program (BNPB, Kemensos) + sumber.
Narasi: "Cekarah memandu warga menemukan bantuan yang tersedia — penyedia, jenis, dan syaratnya."

**Scene 7 — Demo Kategori 5 (penolakan di luar konteks).**
Tampilkan: ketik pertanyaan non-bencana → penolakan sopan.
Narasi: "Cekarah fokus pada bencana. Pertanyaan di luar topik diarahkan kembali — menjaga sistem tetap aman dan tepat guna."

**Scene 8 — Responsible AI.**
Tampilkan: sorot badge keyakinan, chip sumber yang diklik membuka situs resmi, penanda data sintetis.
Narasi: "Setiap jawaban menyertakan sumber dan tingkat keyakinan. Bila data belum ada, Cekarah jujur mengatakannya — bukan mengarang. AI memandu, petugas manusia yang memutuskan."

**Scene 9 — Penutup/CTA.**
Tampilkan: tagline + identitas visual (navy + aksen merah).
Narasi: "Cekarah — navigator awal warga di 48 jam pertama yang menentukan."

---

## 4. Catatan Teknis untuk Eksekusi Remotion (sesi berikutnya)

- **Rekaman layar:** rekam ulang demo dari aplikasi terbaru agar menampilkan streaming, peta, dan sumber yang bisa diklik. Peta memakai **Leaflet + OpenStreetMap** — aktif tanpa API key, jadi langsung bisa direkam.
- **Konsistensi visual:** samakan palet dengan UI terbaru — navy `#0A0F1E`, aksen merah `#E63946`, biru interaktif `#3B82F6`.
- **Durasi:** prioritaskan Scene 5 (peta) sebagai momen puncak; jaga total durasi sesuai ketentuan video pitch.
- **Sebelum mengubah apa pun:** audit dulu struktur scene project Remotion yang sebenarnya (bagian 0), lalu petakan ke penanda KEEP/UPDATE/REMOVE (bagian 2) dan storyboard (bagian 3).
