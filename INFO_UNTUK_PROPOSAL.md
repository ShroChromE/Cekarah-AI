# Info untuk Proposal Cekarah — Bahan Tambahan & Koreksi

> Dokumen ringkas berisi hal-hal **aktual di kode/database** yang bisa Anda **tambahkan atau ubah** di proposal. Semua poin sudah diverifikasi terhadap implementasi (bukan rencana). Diperbarui: 2026-06-25.

---

## 1. Koreksi yang HARUS diubah di proposal (klaim lama sudah tidak akurat)

| Bagian proposal | Klaim lama (keliru/usang) | Ganti dengan (aktual) |
|---|---|---|
| Arsitektur AI — RAG semantic | "Karena pgvector belum tersedia, embedding disimpan sebagai JSONB dan dihitung di sisi aplikasi (PHP)" | **pgvector AKTIF.** Kolom `embedding` bertipe `vector(3072)`; kemiripan kosinus dihitung di PostgreSQL dengan operator `<=>`. |
| Model embedding | "text-embedding-004" | **Model embedding Gemini (3072 dimensi)** via Laravel AI SDK. (text-embedding-004 hanya 768 dimensi — tidak sesuai dengan yang dipakai.) |

---

## 2. Fitur yang perlu DITAMBAHKAN ke proposal (sudah berfungsi, tapi belum disebut)

Proposal versi sekarang hanya menjelaskan chat publik. Padahal tiga fitur berikut sudah jalan dan justru paling memperkuat rubrik penilaian:

### a. Portal Relawan — Human-in-the-Loop (paling kuat untuk Responsible AI)
- Dashboard terpisah (login admin/relawan), berbeda dari chat publik warga.
- Relawan dapat **menambah/menyunting** data posko, program bantuan, dan klaim hasil cek fakta — **wajib menyertakan sumber**.
- Data baru **langsung disinkronkan ke basis pengetahuan RAG** (embedding dibuat otomatis), sehingga jawaban chat warga membaik tanpa re-deploy.
- **Review Queue ("Perlu Ditinjau"):** setiap pertanyaan warga yang dijawab "belum ada data resmi" otomatis masuk antrean agar relawan menambahkan data resminya.
- **Audit trail:** tercatat siapa mengubah data apa dan kapan.
- → Ini bukti konkret syarat panduan *"di mana penilaian manusia tetap berperan"*.

### b. Radar Tren Hoaks & Kebutuhan (memperkuat Kreativitas & Inovasi)
- Mengagregasi log interaksi untuk mendeteksi **lonjakan klaim hoaks sejenis** (dikelompokkan berdasarkan kemiripan, bukan kata-per-kata) dan **lonjakan kebutuhan per wilayah** (posko/bansos).
- Disajikan sebagai sinyal **"Perlu perhatian"** untuk ditindaklanjuti manusia — **bukan** kepastian statistik (jujur secara etis).
- Memposisikan Cekarah sebagai **sistem deteksi dini**, bukan sekadar chatbot tanya-jawab.

### c. Skrip Evaluasi Otomatis (memperkuat Fungsionalitas & Pemanfaatan AI)
- Perintah `php artisan cekarah:evaluate` menguji sistem terhadap 40 pertanyaan ground-truth dan menghasilkan metrik aktual (lihat bagian 3).

### d. Penguatan Responsible AI yang sudah ada di kode (bisa diklaim dengan bukti)
- **Redaksi data pribadi:** sistem otomatis menyensor pola NIK/KK (16 digit) dari pesan **sebelum** diproses model maupun disimpan.
- **Eskalasi darurat berbasis deteksi:** bila pesan mengandung indikasi mengancam jiwa (mis. "terjebak banjir", "hanyut"), sistem memaksa menampilkan **kontak darurat resmi** (dari tabel `emergency_contacts`: BNPB 117, Basarnas 115, Ambulans 119, dll.).
- **Disclaimer medis & hukum:** AI dilarang memberi diagnosis/nasihat medis atau hukum definitif; diarahkan ke pihak berwenang.
- **Transparansi keyakinan:** setiap jawaban menampilkan tingkat keyakinan; bila < 60%, sistem menyarankan verifikasi ke sumber resmi.

---

## 3. Hasil Tes / Evaluasi Aktual (siap tempel ke bagian "Tolok Ukur")

Diukur otomatis via `php artisan cekarah:evaluate` atas **40 kasus uji** ground-truth:

| Metrik keseluruhan | Hasil |
|---|---|
| Akurasi klasifikasi intent | **95%** |
| Jawaban menyertakan sumber/rujukan | **100%** |
| Akurasi status verifikasi klaim | **100%** |
| Keberhasilan menolak pertanyaan di luar konteks | **100%** |
| Error (overload/timeout) | **0 dari 40** |
| Latensi rata-rata | ~31 detik |

| Kategori | N | Akurasi Intent | Sitasi | Latensi |
|---|---|---|---|---|
| Informasi bencana | 8 | 100% | 100% | ~48 dtk |
| Verifikasi klaim | 8 | 100% (status 100%) | 100% | ~26 dtk |
| Lokasi posko | 8 | 100% | 100% | ~27 dtk |
| Bantuan sosial | 8 | **75%** | 100% | ~44 dtk |
| Di luar konteks | 8 | 100% | — | ~10 dtk |

**Catatan jujur (bisa dipakai sebagai poin pengembangan):**
- Kategori *Bantuan Sosial* masih **75%** (2 dari 8 salah klasifikasi) → prioritas perbaikan berikutnya.
- **Sitasi 100% di semua kategori** membuktikan prinsip *grounding* benar-benar berjalan.
- **Latensi ~31 detik** adalah konsekuensi *grounding* riset web langsung ke sumber resmi — trade-off demi akurasi, dan menjadi area optimasi (caching/paralelisasi).

---

## 4. Target Pengguna & Apa yang Bisa Mereka Lakukan di Sistem

Ringkasan konkret per segmen, dipetakan ke fitur nyata yang sudah ada.

### A. Warga Terdampak & Keluarga (prioritas utama — tanpa login)
**Akses:** halaman chat publik (`/chat`). Cukup mengetik pertanyaan, sistem mengenali kebutuhan secara otomatis.
**Yang bisa dilakukan:**
- **Tanya informasi bencana terkini** ("Bagaimana situasi banjir di Aceh?") → ringkasan dari data resmi + sumber & tanggal.
- **Verifikasi kabar/hoaks** ("Benarkah air laut naik di Pidie Jaya?") → penjelasan beralasan + rujukan resmi (bukan vonis sepihak).
- **Cari lokasi posko/pengungsian** ("Posko di Aceh Tamiang di mana?") → daftar lokasi + **peta interaktif** dengan koordinat resmi.
- **Cari informasi bantuan sosial** ("Cara cek bansos PKH/BPNT?") → program, penyedia, syarat, dan sumber — tanpa diminta data pribadi.
- **Mendapat arahan darurat** otomatis (kontak BNPB 117/Basarnas 115) bila terdeteksi situasi mengancam jiwa.
- Terlindungi: NIK/data sensitif yang tak sengaja diketik akan otomatis disensor.

### B. Relawan & Organisasi Kemanusiaan (PMI, MDMC, Tagana — dengan login)
**Akses:** Portal Relawan (`/portal`).
**Yang bisa dilakukan:**
- **Mengelola data posko** (`/portal/shelters`) — tambah/sunting lokasi + koordinat (klik di peta).
- **Mengelola program bantuan** (`/portal/aid`).
- **Mengelola hasil cek fakta/klaim** (`/portal/claims`) — dengan sumber wajib; otomatis masuk RAG.
- **Menindaklanjuti Review Queue** (`/portal/review`) — melihat pertanyaan warga yang belum ada datanya, lalu menambahkan data resminya (jawaban warga langsung membaik).
- **Memantau Radar Tren** (`/portal/radar`) — melihat klaim hoaks & wilayah yang kebutuhannya sedang naik, sebagai sinyal deteksi dini untuk dikoordinasikan di lapangan.
- Semua perubahan terekam dalam audit trail.

### C. Masyarakat Umum / "Penjaga Gerbang" Informasi (tanpa login)
**Akses:** sama seperti warga — chat publik (`/chat`).
**Yang bisa dilakukan:**
- **Memverifikasi klaim sebelum membagikannya** ke grup/media sosial, sehingga tidak ikut menyebarkan hoaks. Mendapat penjelasan + sumber sebagai bahan edukasi literasi digital.

### D. Admin / Internal (dengan login)
**Yang bisa dilakukan:**
- Seluruh kemampuan relawan, plus menjalankan **evaluasi otomatis** (`php artisan cekarah:evaluate`) untuk mengukur kualitas sistem secara berkala.

---
