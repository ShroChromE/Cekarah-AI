<?php

/**
 * Ground-truth evaluation set for `php artisan cekarah:evaluate`.
 *
 * Each case: question + expected intent category. Claim cases carry an expected
 * verification status ONLY when the claim exists in the seeded dataset; open
 * claims are left status-less (scored on intent + citation only). Aligned to the
 * real Sumatera 2025–2026 dataset (Aceh/Sumut/Sumbar) — no longer Binjai.
 *
 * Intent categories: disaster_info | claim_verification | shelter_location
 *                    | aid_assistance | out_of_scope
 */
return [
    // 1. INFORMASI BENCANA --------------------------------------------------
    ['q' => 'Banjir dan longsor sedang terjadi di mana saja di Sumatera?', 'intent' => 'disaster_info'],
    ['q' => 'Bagaimana situasi banjir di Sumatera Utara saat ini?', 'intent' => 'disaster_info'],
    ['q' => 'Bagaimana kondisi bencana banjir dan longsor di Aceh?', 'intent' => 'disaster_info'],
    ['q' => 'Apa kabar pemulihan pascabencana di Sumatera Barat?', 'intent' => 'disaster_info'],
    ['q' => 'Berapa jumlah korban dan pengungsi akibat bencana Sumatera?', 'intent' => 'disaster_info'],
    ['q' => 'Jalan Padang–Bukittinggi via Malalak apakah bisa dilewati?', 'intent' => 'disaster_info'],
    ['q' => 'Apakah Jembatan Tenge Besi di Bener Meriah masih terputus?', 'intent' => 'disaster_info'],
    ['q' => 'Adakah informasi gempa atau cuaca ekstrem terbaru dari BMKG?', 'intent' => 'disaster_info'],

    // 2. VERIFIKASI KLAIM ---------------------------------------------------
    ['q' => 'Benarkah air laut di Pidie Jaya Aceh naik dan akan terjadi tsunami?', 'intent' => 'claim_verification', 'status' => 'hoax'],
    ['q' => 'Katanya air laut naik bak tsunami di pantai utara Jawa Tengah, benar?', 'intent' => 'claim_verification', 'status' => 'hoax'],
    ['q' => 'Ada tautan pendaftaran Internet Rakyat gratis 3 bulan, itu asli atau hoaks?', 'intent' => 'claim_verification', 'status' => 'hoax'],
    ['q' => 'Apakah benar akan ada gempa susulan dahsyat malam ini?', 'intent' => 'claim_verification'],
    ['q' => 'Viral pesan WhatsApp peringatan tsunami malam ini, ini valid atau hoaks?', 'intent' => 'claim_verification'],
    ['q' => 'Benarkah ada rekening donasi resmi untuk korban banjir yang disebar di grup?', 'intent' => 'claim_verification'],
    ['q' => 'Tolong cek kebenaran kabar bahwa bendungan akan dibuka dan menyebabkan banjir', 'intent' => 'claim_verification'],
    ['q' => 'Apa betul BMKG mengeluarkan peringatan dini gempa untuk besok pagi?', 'intent' => 'claim_verification'],

    // 3. LOKASI POSKO / SHELTER --------------------------------------------
    ['q' => 'Posko pengungsian di Aceh Tamiang di mana?', 'intent' => 'shelter_location'],
    ['q' => 'Di mana posko pengungsian di Padang Panjang?', 'intent' => 'shelter_location'],
    ['q' => 'Posko di Agam ada di mana?', 'intent' => 'shelter_location'],
    ['q' => 'Pos koordinasi bencana di Medan di mana?', 'intent' => 'shelter_location'],
    ['q' => 'Tempat mengungsi di Banda Aceh?', 'intent' => 'shelter_location'],
    ['q' => 'Lokasi posko di Kota Padang?', 'intent' => 'shelter_location'],
    ['q' => 'Posko pengungsian terdekat di Aceh Besar?', 'intent' => 'shelter_location'],
    ['q' => 'Ke mana saya harus mengungsi jika rumah saya di Aceh Tamiang kebanjiran?', 'intent' => 'shelter_location'],

    // 4. BANTUAN SOSIAL / BANSOS -------------------------------------------
    ['q' => 'Saya korban bencana, bagaimana cara cek bansos PKH atau BPNT?', 'intent' => 'aid_assistance'],
    ['q' => 'Berapa nominal bantuan BPNT dan PKH tahun 2026?', 'intent' => 'aid_assistance'],
    ['q' => 'Apa itu Dana Tunggu Hunian (DTH) dan siapa yang berhak?', 'intent' => 'aid_assistance'],
    ['q' => 'Bagaimana mekanisme penyaluran bantuan pascabencana?', 'intent' => 'aid_assistance'],
    ['q' => 'Apa itu desil DTSEN dalam penentuan bansos?', 'intent' => 'aid_assistance'],
    ['q' => 'Saya korban banjir, bantuan apa yang bisa saya terima?', 'intent' => 'aid_assistance'],
    ['q' => 'Bagaimana cara cek status penerima bantuan sosial?', 'intent' => 'aid_assistance'],
    ['q' => 'Apa syarat menerima bantuan tunai untuk korban bencana?', 'intent' => 'aid_assistance'],

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
