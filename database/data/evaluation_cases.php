<?php

/**
 * Ground-truth evaluation set for `php artisan cekarah:evaluate`.
 *
 * Each case: question + expected intent category. Claim cases also carry an
 * expected verification status. Citations are expected for every in-scope
 * category (1-4) and never for out-of-scope (5).
 *
 * Intent categories: disaster_info | claim_verification | shelter_location
 *                    | aid_assistance | out_of_scope
 */
return [
    // 1. INFORMASI BENCANA --------------------------------------------------
    ['q' => 'Banjir sedang terjadi di mana saja?', 'intent' => 'disaster_info'],
    ['q' => 'Bagaimana situasi banjir di Sumatera Utara saat ini?', 'intent' => 'disaster_info'],
    ['q' => 'Apakah ada bencana banjir di Binjai?', 'intent' => 'disaster_info'],
    ['q' => 'Wilayah mana saja yang terdampak banjir di Sumatera?', 'intent' => 'disaster_info'],
    ['q' => 'Apa status tanggap darurat bencana di Langkat?', 'intent' => 'disaster_info'],
    ['q' => 'Adakah informasi gempa terbaru di Indonesia?', 'intent' => 'disaster_info'],
    ['q' => 'Bagaimana kondisi terkini bencana di Tapanuli Selatan?', 'intent' => 'disaster_info'],
    ['q' => 'Berikan informasi cuaca ekstrem terbaru dari BMKG', 'intent' => 'disaster_info'],

    // 2. VERIFIKASI KLAIM ---------------------------------------------------
    ['q' => 'Benarkah air laut di Pidie Jaya Aceh naik dan akan terjadi tsunami?', 'intent' => 'claim_verification', 'status' => 'hoax'],
    ['q' => 'Kata teman saya akan ada banjir besar di Binjai hari ini, benar tidak?', 'intent' => 'claim_verification', 'status' => 'no_official_data'],
    ['q' => 'Apakah benar akan ada gempa susulan dahsyat malam ini?', 'intent' => 'claim_verification', 'status' => 'hoax'],
    ['q' => 'Beredar kabar Bendungan Namo Rambe Deli Serdang akan jebol, benarkah?', 'intent' => 'claim_verification', 'status' => 'hoax'],
    ['q' => 'Viral pesan WhatsApp peringatan tsunami malam ini, ini valid atau hoaks?', 'intent' => 'claim_verification'],
    ['q' => 'Benarkah ada rekening donasi resmi untuk korban banjir yang disebar di grup?', 'intent' => 'claim_verification'],
    ['q' => 'Tolong cek kebenaran kabar bahwa bendungan akan dibuka dan menyebabkan banjir', 'intent' => 'claim_verification'],
    ['q' => 'Apa betul BMKG mengeluarkan peringatan dini gempa untuk besok pagi?', 'intent' => 'claim_verification', 'status' => 'hoax'],

    // 3. LOKASI POSKO / SHELTER --------------------------------------------
    ['q' => 'Posko pengungsian di Binjai di mana?', 'intent' => 'shelter_location'],
    ['q' => 'Di mana lokasi dapur umum untuk korban banjir di Binjai?', 'intent' => 'shelter_location'],
    ['q' => 'Saya butuh pos kesehatan terdekat di Binjai', 'intent' => 'shelter_location'],
    ['q' => 'Tempat pengungsian yang tersedia di Kota Binjai?', 'intent' => 'shelter_location'],
    ['q' => 'Ada shelter atau tempat mengungsi di Binjai Timur?', 'intent' => 'shelter_location'],
    ['q' => 'Lokasi posko bantuan bencana di Binjai', 'intent' => 'shelter_location'],
    ['q' => 'Ke mana saya harus mengungsi jika rumah saya di Binjai kebanjiran?', 'intent' => 'shelter_location'],
    ['q' => 'Daftar titik kumpul evakuasi di Binjai', 'intent' => 'shelter_location'],

    // 4. BANTUAN SOSIAL / BANSOS -------------------------------------------
    ['q' => 'Daerah saya di Binjai kena bencana, ada bantuan apa?', 'intent' => 'aid_assistance'],
    ['q' => 'Bantuan sosial apa yang tersedia untuk korban banjir di Binjai?', 'intent' => 'aid_assistance'],
    ['q' => 'Bagaimana cara mendapat bantuan logistik darurat di Binjai?', 'intent' => 'aid_assistance'],
    ['q' => 'Apakah ada bantuan tunai dari Kemensos untuk korban bencana Binjai?', 'intent' => 'aid_assistance'],
    ['q' => 'Program bansos untuk warga terdampak banjir di Binjai?', 'intent' => 'aid_assistance'],
    ['q' => 'Saya korban banjir, bantuan pangan apa yang bisa saya terima?', 'intent' => 'aid_assistance'],
    ['q' => 'Apa syarat menerima bantuan bencana di Binjai?', 'intent' => 'aid_assistance'],
    ['q' => 'Bantuan dari BNPB untuk pengungsi di Binjai apa saja?', 'intent' => 'aid_assistance'],

    // 5. DI LUAR KONTEKS ----------------------------------------------------
    ['q' => 'Siapa presiden Indonesia saat ini?', 'intent' => 'out_of_scope'],
    ['q' => 'Tolong buatkan saya puisi tentang kucing', 'intent' => 'out_of_scope'],
    ['q' => 'Berapa hasil 245 dikali 17?', 'intent' => 'out_of_scope'],
    ['q' => 'Resep rendang yang enak bagaimana?', 'intent' => 'out_of_scope'],
    ['q' => 'Rekomendasikan film action terbaik tahun ini', 'intent' => 'out_of_scope'],
    ['q' => 'Bagaimana cara investasi saham untuk pemula?', 'intent' => 'out_of_scope'],
    ['q' => 'Siapa pemain bola terbaik di dunia?', 'intent' => 'out_of_scope'],
    ['q' => 'Terjemahkan "selamat pagi" ke bahasa Jepang', 'intent' => 'out_of_scope'],
];
