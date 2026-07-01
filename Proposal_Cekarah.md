**Cekarah: Asisten AI Navigasi Bantuan & Verifikasi Hoaks Pasca Bencana**

1. **Latar Belakang & Rumusan Masalah**

Tim kami memilih Studi Kasus 1 yaitu “Komunitas: Kesulitan Mengakses Informasi yang Valid sebagai dasar pengembangan solusi AI yang membantu masyarakat mengakses informasi yang valid secara cepat dan mudah”. Informasi yang cepat dan akurat sangat penting dalam penanganan bencana. Namun, saat bencana terjadi, masyarakat sering kesulitan memperoleh informasi mengenai langkah yang harus dilakukan, lokasi pengungsian, bantuan yang tersedia, maupun lembaga yang dapat dihubungi.

Kondisi tersebut terlihat pada bencana hidrometeorologi yang melanda Sumatera pada periode 2025–2026. Bencana ini menunjukkan bahwa masyarakat masih kesulitan memperoleh informasi yang valid dan dapat segera ditindaklanjuti.

Pada 48 jam pertama pascabencana, kebutuhan informasi menjadi sangat krusial. Masyarakat membutuhkan panduan mengenai evakuasi, bantuan logistik, layanan kesehatan, serta prosedur bantuan sosial. Di sisi lain, penyebaran informasi yang tidak terverifikasi menyebabkan hoaks mudah dipercaya dan memperburuk keadaan.

Berdasarkan kondisi tersebut, terdapat dua masalah utama:

* **Navigasi Bantuan**

Masyarakat kesulitan mengetahui lokasi bantuan, layanan kesehatan, dan prosedur bantuan sosial.

* **Verifikasi Informasi**

Masyarakat sulit membedakan informasi yang benar dengan hoaks yang beredar melalui media sosial dan pesan berantai.

1. **Target Pengguna**

|  |  |  |
| --- | --- | --- |
| **Segmen Pengguna** | **Kebutuhan Utama** | **Bagaimana Cekarah Membantu** |
| Warga terdampak & keluarga (prioritas utama, 48 jam pertama) | Informasi evakuasi, posko, air bersih, kesehatan; verifikasi kabar yang masuk | Jawaban cepat berbasis data resmi + cek fakta instan via *chat* |
| Relawan & organisasi kemanusiaan (PMI, MDMC, Tagana) | Data titik kebutuhan, prosedur koordinasi klaster | Rujukan cepat ke prosedur & sumber resmi |
| Masyarakat umum / "penjaga gerbang" informasi | Memverifikasi sebelum membagikan (mencegah jadi penyebar hoaks) | Fitur verifikasi klaim dengan penjelasan + sumber, mengedukasi literasi digital |

Berbeda dengan asisten AI umum, Cekarah secara eksplisit dirancang sebagai alat khusus krisis. Karena itu, sistem ini menolak pertanyaan di luar konteks kebencanaan untuk menjaga fokus, akurasi, dan keamanan penggunaan.

1. **Ide Solusi Berbasis AI**

Cekarah (“cek” + “arah”) merupakan asisten AI berbasis percakapan yang menggunakan *routing intent* otomatis.

|  |  |
| --- | --- |
| **Kategori** | **Layanan** |
| Informasi bencana | Sistem mencari informasi bencana terkini beserta sumber resmi |
| Verifikasi klaim | Sistem mengecek klaim atau hoaks dengan penjelasan dan rujukan |
| Lokasi posko | Sistem menampilkan lokasi posko dan shelter melalui peta interaktif |
| Bantuan sosial | Sistem menjelaskan informasi bantuan sosial yang tersedia di wilayah terdampak |
| Di luar konteks | Sistem menolak dengan sopan dan mengarahkan kembali ke topik bencana |

Respons ditampilkan secara streaming sehingga pengguna dapat melihat jawaban muncul bertahap dan memperoleh umpan balik lebih cepat. Proses pencarian bantuan dan verifikasi informasi didukung oleh *workflow* dan *tool* yang jelas, sehingga dapat dibuktikan melalui implementasi sistem.

1. **Pendekatan Teknis & Arsitektur AI**
2. **Stack Teknologi**

* ***Backend:*** Laravel 13 dengan Laravel AI SDK untuk orkestrasi *prompt*, *tool-calling*, dan manajemen konteks ke Gemini.
* ***Frontend:*** Inertia.js + React untuk antarmuka *chat* reaktif tanpa REST API terpisah.
* **Model *AI:*** Gemini 3 Flash Preview API untuk pemrosesan bahasa Indonesia, *reasoning*, dan jawaban dengan sitasi.
* ***Database & Vektor:*** PostgreSQL + pgvector untuk *embedding* dan *similarity search*.

1. **Arsitektur *RAG (Retrieval-Augmented Generation)***

Alur kerja inti Cekarah:

1. ***Ingestion*:** Dokumen resmi (BNPB, BMKG, Kemensos, PMI, dll.) dipecah menjadi *chunk*, dibuat *embedding*, lalu disimpan di PostgreSQL beserta *metadata* (sumber, *URL*, tanggal).
2. ***Retrieval*:** Query pengguna diubah menjadi *embedding* untuk mencari potongan dokumen paling relevan.
3. ***Generation*:** Konteks hasil *retrieval* + pertanyaan dikirim ke Gemini dengan instruksi ketat agar hanya menjawab berdasarkan data, selalu menyertakan sumber, dan menyatakan “tidak ada data resmi” jika kosong.
4. ***Response*:** Jawaban ditampilkan di UI React dengan kartu sumber yang dapat diklik.
5. **Arsitektur Final**
   * ***Tool-calling* Gemini:** *Routing intent* menggunakan 4 *tool* utama (search\_disaster\_info, verify\_claim, find\_shelter\_locations, get\_aid\_assistance\_info) berdasarkan *reasoning* model, bukan *keyword*.
   * ***Grounding data*:** Semua jawaban berasal dari hasil *tool* atau *database*, bukan pengetahuan model. Jika tidak ditemukan data maka sistem akan menjawab “belum ada data resmi”.
   * ***RAG semantic*:** Menggunakan *embedding* *text-embedding-004* dengan *cosine similarity*. Karena pgvector belum tersedia, *embedding* disimpan sebagai *JSONB* dan dihitung di sisi aplikasi (PHP), cukup untuk *demo*.
   * **Skema *Database*:** Menggunakan *disaster\_events* sebagai pusat relasi yang menghubungkan *shelter\_locations*, *aid\_programs*, dan *claim\_verifications*. Referensi sumber dikelola melalui *tabel sources* dan *citations* (relasi polimorfik) agar dapat digunakan lintas kategori, sementara *intent\_logs* menyimpan hasil klasifikasi intent setiap pesan.
   * **Model:** Gemini-3-flash-preview + text-embedding-004 melalui Laravel AI SDK sebagai framework integrasi AI.
6. **Sumber Data (*Knowledge Base* *RAG*)**

|  |  |  |  |
| --- | --- | --- | --- |
| **Cakupan data** | **Judul** | **Lembaga/Sumber Resmi** | **Link Sumber** |
| **Info dan Kondisi Bencana Alam** | Banjir dan Longsor Sumatera | BNPB | [bnpb.go.id](https://www.bnpb.go.id/berita/pasca-bencana-di-sumatra-bnpb-percepat-hunian-infrastruktur-vital-dan-operasi-modifikasi-cuaca) |
| Logistik Posko Sumatera Barat | BNPB | [bnpb.go.id](https://www.bnpb.go.id/berita/bantuan-korban-bencana-sumbar-harus-terkoordinasi-melalui-posko) |
| Pembangunan Huntara Sumatera Barat | Kemenko PMK | [kemenkopmk.go.id](https://www.kemenkopmk.go.id/menko-pmk-resmikan-hunian-sementara-bagi-masyarakat-terdampak-bencana-di-sumatra-barat) |
| Fase Pemulihan Pascabencana Sumatera | Kemendagri via Kompas | [kompas.com](https://kilaskementerian.kompas.com/kemendagri/read/2026/05/25/19005141/mendagri-pastikan-pemulihan-pascabencana-sumatera-masuk-tahap-pemulihan) |
| Kesiapsiagaan Kekeringan dan Kebakaran Hutan | BNPB | [bnpb.go.id](https://bnpb.go.id/berita/perkembangan-situasi-dan-penanganan-bencana-di-tanah-air-7-juni-2026) |
| Portal Bencana Real-time | Geoportal & Portal Satu Data BNPB | [data.bnpb.go.id](https://data.bnpb.go.id/) |
| Akses Terdampak dan Jembatan Putus | Portal Satu Data BNPB | [xlsx-titik-terdampak-dan-posko-pengungsian](https://www.google.com/search?q=%5Bhttps://data.bnpb.go.id/dataset/9144c8ed-4c4d-43f5-acc7-c6486324f0ad/resource/8444ab39-70b3-4e24-ac0e-335308bb2170/download/titik-terdampak-dan-posko-pengungsian-bansor-sumatera-2025.xlsx%5D(https://data.bnpb.go.id/dataset/9144c8ed-4c4d-43f5-acc7-c6486324f0ad/resource/8444ab39-70b3-4e24-ac0e-335308bb2170/download/titik-terdampak-dan-posko-pengungsian-bansor-sumatera-2025.xlsx)) |
| **Daftar Posko dan Tempat Pengungsian** | Kantor Gubernur Sumatera Barat | BNPB (Portal Satu Data Bencana Indonesia) | [xlsx-titik-terdampak-dan-posko-pengungsian](https://www.google.com/search?q=%5Bhttps://data.bnpb.go.id/dataset/9144c8ed-4c4d-43f5-acc7-c6486324f0ad/resource/8444ab39-70b3-4e24-ac0e-335308bb2170/download/titik-terdampak-dan-posko-pengungsian-bansor-sumatera-2025.xlsx%5D(https://data.bnpb.go.id/dataset/9144c8ed-4c4d-43f5-acc7-c6486324f0ad/resource/8444ab39-70b3-4e24-ac0e-335308bb2170/download/titik-terdampak-dan-posko-pengungsian-bansor-sumatera-2025.xlsx)) |
| BPBD Provinsi Sumatera Utara |
| Kantor Gubernur Sumatera Utara |
| Kantor Gubernur Aceh |
| Kodim Tapanuli Utara |
| Pusdalops BNPB Sumatera |
| Lanud Sultan Iskandar Muda |
| Salareh Aia |
| SMU 1 Rantau |
| Kantor Lurah Pasar Usang |
| **Jenis dan Aturan Bantuan Sosial** | Cara Cek Status Penerima PKH dan BPNT | Kementerian Sosial RI | [cekbansos.kemensos.go.id](https://cekbansos.kemensos.go.id/) |
| Penentuan Sasaran Desil DTSEN | Kemensos via RRI | [rri.co.id](https://rri.co.id/nasional/2437631/cara-cek-penerima-bansos-bpnt-pkh-triwulan-ii-2026-akses-cekbansoskemensosgoid) |
| Nominal Bantuan BPNT dan PKH 2026 | Kemensos via Kompas TV | [kompas.tv](https://www.kompas.tv/info-publik/672308/cara-cek-bansos-pkh-bpnt-juni-2026-pakai-data-ktp-sekalian-cek-status-desil-dtsen) |
| Skema Dana Tunggu Hunian | BNPB via Media Indonesia | [mediaindonesia.com](https://mediaindonesia.com/nusantara/867085/panduan-lengkap-skema-dana-tunggu-hunian-dth-bnpb-dan-syarat-pengajuannya) |
| Verifikasi Data Penerima Bertahap | Kemendagri/Satgas PRR via Detik | [detik.com](https://news.detik.com/berita/d-8479371/bantuan-pascabencana-mengalir-bertahap-pemda-diminta-terus-perbarui-data) |
| **Klarifikasi Berita Hoaks** | Hoaks Tsunami Pidie Jaya | Komdigi | [komdigi.go.id](https://www.komdigi.go.id/berita/berita-hoaks/detail/hoaks-air-laut-naik-di-wilayah-kabupaten-pidie-jaya) |
| Hoaks Air Laut Pantai Utara | Komdigi | [komdigi.go.id](https://www.komdigi.go.id/berita/berita-hoaks/detail/hoaks-air-laut-naik-bak-tsunami-sapu-pantai-utara-jawa-tengah) |
| Hoaks Internet Gratis 3 Bulan | Komdigi | [komdigi.go.id](https://www.komdigi.go.id/berita/berita-komdigi/detail/ini-hoaks-pendaftaran-internet-rakyat-gratis-3-bulan) |

1. ***Responsible AI***

Cekarah mengadopsi prinsip AI yang bertanggung jawab, khususnya karena beroperasi dalam konteks darurat yang menyangkut keselamatan jiwa:

* **Transparansi Sumber**

Informasi yang digunakan berisiko tidak akurat atau sulit diverifikasi. Untuk mengatasinya, kami mencantumkan sumber, tanggal publikasi, dan tautan rujukan pada setiap jawaban. Jika data resmi belum tersedia, sistem menyatakannya secara terbuka dan mengarahkan pengguna ke kanal resmi.

* **Anti-Halusinasi (*Grounded AI*)**

Model AI berisiko menghasilkan informasi yang tidak sesuai fakta (halusinasi). Untuk mengatasinya, kami membatasi jawaban agar hanya disusun dari data yang ditemukan melalui basis data dan mekanisme *retrieval*. Jika informasi tidak tersedia, sistem menyatakan belum ada data resmi tanpa membuat asumsi.

* **Verifikasi Berbasis Penjelasan**

Hasil verifikasi yang hanya berupa label "hoaks" atau "fakta" berisiko menimbulkan kesalahpahaman. Untuk mengatasinya, kami menyertakan penjelasan, alasan, dan rujukan pendukung sehingga pengguna memahami dasar setiap hasil verifikasi.

* **Eskalasi Situasi Darurat**

Pengguna berisiko terlambat memperoleh bantuan jika hanya mengandalkan AI dalam situasi darurat. Untuk mengatasinya, kami mengarahkan pengguna ke layanan dan kontak darurat resmi saat terdeteksi kondisi yang mengancam keselamatan jiwa.

* **Transparansi Keyakinan**

Pengguna berisiko terlalu percaya pada jawaban AI meski tingkat kepastiannya rendah. Untuk mengatasinya, kami menampilkan tingkat keyakinan pada setiap jawaban. Jika nilainya di bawah 60%, sistem menyarankan verifikasi melalui sumber resmi.

* **Privasi dan Keamanan Data**

Pengumpulan data pribadi berisiko mengurangi privasi pengguna. Untuk mengatasinya, kami meminimalkan data yang dikumpulkan serta menggunakan identifikasi sesi acak untuk melindungi identitas pengguna.

* **Penolakan di Luar Lingkup**

Pertanyaan di luar kebencanaan berisiko menghasilkan jawaban yang tidak akurat. Untuk mengatasinya, kami membatasi layanan pada topik kebencanaan dan menolak pertanyaan di luar lingkup secara sopan.

* **Auditabilitas**

Proses AI berisiko sulit dievaluasi tanpa jejak penggunaan. Untuk mengatasinya, kami mencatat kategori *intent* dan penggunaan *tool* pada tabel *intent\_logs* untuk evaluasi dan pengembangan sistem.