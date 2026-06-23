import { Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Landing() {
    const [scrolled, setScrolled] = useState(false);

    useEffect(() => {
        const handler = () => setScrolled(window.scrollY > 20);
        handler();
        window.addEventListener('scroll', handler, { passive: true });
        return () => window.removeEventListener('scroll', handler);
    }, []);

    return (
        <div className="min-h-screen bg-white">
            {/* Sticky Navbar — transparent over hero, solid once scrolled */}
            <nav
                className={`fixed top-0 z-50 w-full px-6 py-4 transition-all duration-300 ${
                    scrolled
                        ? 'border-b border-slate-800/50 backdrop-blur-md'
                        : 'border-b border-transparent'
                }`}
                style={{
                    backgroundColor: scrolled
                        ? 'rgba(10,15,30,0.95)'
                        : 'transparent',
                }}
            >
                <div className="mx-auto flex max-w-6xl items-center justify-between">
                    <span className="text-base font-semibold tracking-tight text-white">
                        Cekarah
                    </span>
                    <div className="flex items-center gap-6">
                        <Link
                            href="/about"
                            className="text-sm text-slate-400 transition-colors hover:text-white"
                        >
                            Tentang
                        </Link>
                        <Link
                            href="/chat"
                            className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700"
                        >
                            Buka Chat
                        </Link>
                    </div>
                </div>
            </nav>

            {/* SECTION 1 — HERO */}
            <section
                id="hero"
                className="flex min-h-screen items-center pt-16"
                style={{ backgroundColor: '#0A0F1E' }}
            >
                <div className="mx-auto w-full max-w-6xl px-6 py-20">
                    <div className="flex flex-col gap-16 lg:flex-row lg:items-center">
                        {/* Left column */}
                        <div className="lg:w-3/5">
                            <p
                                className="mb-6 text-xs text-slate-400"
                                style={{ letterSpacing: '0.15em' }}
                            >
                                EKSHIBISI KA — LKS DIKMEN NASIONAL 2026
                            </p>
                            <h1
                                className="font-black text-white"
                                style={{
                                    fontSize: 'clamp(2.5rem, 7vw, 4.5rem)',
                                    letterSpacing: '-0.03em',
                                    lineHeight: 1.0,
                                }}
                            >
                                48 jam pertama
                                <br />
                                yang menentukan.
                            </h1>
                            <p className="mt-6 max-w-md text-lg leading-relaxed text-slate-400">
                                Cekarah membantu warga menemukan bantuan resmi
                                dan memverifikasi informasi dalam situasi
                                darurat bencana.
                            </p>
                            <div className="mt-10 flex items-center gap-6">
                                <Link
                                    href="/chat"
                                    className="rounded-lg px-8 py-4 text-base font-semibold text-white transition-colors"
                                    style={{ backgroundColor: '#E63946' }}
                                    onMouseOver={(e) =>
                                        (e.currentTarget.style.backgroundColor =
                                            '#c1121f')
                                    }
                                    onMouseOut={(e) =>
                                        (e.currentTarget.style.backgroundColor =
                                            '#E63946')
                                    }
                                >
                                    Mulai sekarang →
                                </Link>
                                <Link
                                    href="/about"
                                    className="text-sm text-slate-400 underline transition-colors hover:text-slate-200"
                                >
                                    Pelajari cara kerja
                                </Link>
                            </div>
                        </div>

                        {/* Right column — stat ticker (desktop only) */}
                        <div className="hidden lg:block lg:w-2/5">
                            <div className="space-y-0">
                                {[
                                    {
                                        num: '1.199',
                                        label: 'korban meninggal, bencana Sumatera Nov 2025',
                                    },
                                    {
                                        num: '114.200',
                                        label: 'warga mengungsi dalam 48 jam pertama',
                                    },
                                    {
                                        num: '1.890',
                                        label: 'konten hoaks teridentifikasi (Okt 2024–Des 2025)',
                                    },
                                ].map((stat, i) => (
                                    <div
                                        key={i}
                                        className="py-6"
                                        style={{
                                            borderTop:
                                                i === 0
                                                    ? 'none'
                                                    : '1px solid rgba(255,255,255,0.1)',
                                        }}
                                    >
                                        <p
                                            className="font-black text-white tabular-nums"
                                            style={{
                                                fontSize: '3.5rem',
                                                lineHeight: 1.1,
                                                letterSpacing: '-0.02em',
                                            }}
                                        >
                                            {stat.num}
                                        </p>
                                        <p className="mt-1 text-sm text-slate-400">
                                            {stat.label}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    className="absolute bottom-0 left-0 w-full"
                    style={{ borderTop: '1px solid', borderColor: '#1e293b' }}
                />
            </section>

            {/* SECTION 2 — MASALAH */}
            <section id="masalah" className="bg-white py-24">
                <div className="mx-auto max-w-6xl px-6">
                    <h2
                        className="max-w-2xl text-3xl font-bold text-slate-900"
                        style={{ letterSpacing: '-0.01em' }}
                    >
                        Dalam 48 jam pertama krisis, warga menghadapi dua
                        masalah sekaligus.
                    </h2>
                    <div className="mt-12 grid gap-12 lg:grid-cols-2">
                        <div className="border-l-4 border-slate-900 pl-6">
                            <p className="mb-2 font-mono text-xs text-slate-400">
                                01
                            </p>
                            <h3 className="text-xl font-bold text-slate-900">
                                Tidak tahu harus kemana
                            </h3>
                            <p className="mt-3 leading-relaxed text-slate-600">
                                Informasi bantuan tersebar di puluhan kanal.
                                Warga kebingungan menentukan lembaga yang tepat,
                                dokumen yang dibutuhkan, dan langkah pertama.
                            </p>
                        </div>
                        <div
                            className="border-l-4 pl-6"
                            style={{ borderLeftColor: '#E63946' }}
                        >
                            <p className="mb-2 font-mono text-xs text-slate-400">
                                02
                            </p>
                            <h3 className="text-xl font-bold text-slate-900">
                                Tidak bisa bedakan mana yang benar
                            </h3>
                            <p className="mt-3 leading-relaxed text-slate-600">
                                Hoaks bencana menyebar lebih cepat dari bantuan.
                                Kasus Pidie Jaya Desember 2025: pesan "air laut
                                naik" memicu evakuasi panik, mengacaukan operasi
                                SAR.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {/* SECTION 3 — CARA KERJA */}
            <section
                id="cara-kerja"
                className="py-24"
                style={{ backgroundColor: '#F0F4FF' }}
            >
                <div className="mx-auto max-w-6xl px-6">
                    <p className="text-xs tracking-widest text-slate-400 uppercase">
                        CARA KERJA
                    </p>
                    <h2
                        className="mt-2 font-black text-slate-900"
                        style={{
                            fontSize: '2.25rem',
                            letterSpacing: '-0.02em',
                        }}
                    >
                        Satu kotak chat. Dua kemampuan.
                    </h2>

                    {/* Flow diagram */}
                    <div className="mt-12 flex flex-col gap-8 lg:flex-row lg:items-start lg:gap-0">
                        {[
                            {
                                num: '01',
                                title: 'Input bebas',
                                desc: 'Tulis situasimu dengan kata-katamu sendiri',
                            },
                            {
                                num: '02',
                                title: 'Deteksi kebutuhan',
                                desc: 'Agent AI menentukan jalur yang tepat secara otomatis',
                            },
                            {
                                num: '03',
                                title: 'Navigasi atau Verifikasi',
                                desc: 'Langkah konkret + sumber resmi + kontak petugas',
                            },
                        ].map((step, i) => (
                            <div
                                key={i}
                                className="flex flex-1 items-start gap-4 lg:flex-col"
                            >
                                <div className="flex-1">
                                    <p className="font-mono text-xs text-slate-400">
                                        {step.num}
                                    </p>
                                    <p className="mt-1 font-semibold text-slate-900">
                                        {step.title}
                                    </p>
                                    <p className="mt-1 text-sm leading-relaxed text-slate-600">
                                        {step.desc}
                                    </p>
                                </div>
                                {i < 2 && (
                                    <div
                                        className="hidden shrink-0 items-center lg:flex"
                                        style={{ width: '3rem' }}
                                    >
                                        <div className="w-full border-t-2 border-dashed border-slate-300" />
                                        <span className="ml-1 text-slate-300">
                                            ›
                                        </span>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>

                    {/* Two-column features */}
                    <div className="mt-16 grid gap-8 lg:grid-cols-2">
                        <div>
                            <h3 className="font-semibold text-slate-900">
                                Navigasi Bantuan
                            </h3>
                            <p className="mt-1 text-sm font-medium text-slate-500">
                                Temukan bantuan yang tepat
                            </p>
                            <ul className="mt-4 space-y-2 text-sm text-slate-600">
                                {[
                                    'Prosedur evakuasi banjir step-by-step',
                                    'Cara daftar bantuan sosial darurat (PKH, BPNT)',
                                    'Kontak resmi BNPB, Basarnas, PMI, Kemensos',
                                ].map((item) => (
                                    <li key={item} className="flex gap-2">
                                        <span className="text-blue-500">→</span>
                                        {item}
                                    </li>
                                ))}
                            </ul>
                        </div>
                        <div>
                            <h3 className="font-semibold text-slate-900">
                                Verifikasi Klaim
                            </h3>
                            <p className="mt-1 text-sm font-medium text-slate-500">
                                Cek sebelum percaya
                            </p>
                            <ul className="mt-4 space-y-2 text-sm text-slate-600">
                                {[
                                    'Cross-check klaim dengan sumber BNPB & BMKG',
                                    'Penjelasan dengan alasan, bukan vonis "hoaks"',
                                    'Rujukan langsung ke sumber resmi',
                                ].map((item) => (
                                    <li key={item} className="flex gap-2">
                                        <span className="text-purple-500">
                                            →
                                        </span>
                                        {item}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            {/* SECTION 4 — DATA STATISTIK */}
            <section
                id="data"
                className="py-24"
                style={{ backgroundColor: '#0F172A' }}
            >
                <div className="mx-auto max-w-6xl px-6">
                    <div className="grid grid-cols-1 gap-8 text-center lg:grid-cols-3">
                        {[
                            {
                                num: '390',
                                label: 'laporan kesejahteraan sosial 2024',
                                sub: '(Ombudsman)',
                            },
                            {
                                num: '1.890',
                                label: 'konten hoaks teridentifikasi',
                                sub: '(Kemkomdigi)',
                            },
                            {
                                num: '5',
                                label: 'orang ditangkap karena hoaks bencana Aceh',
                                sub: '',
                            },
                        ].map((stat) => (
                            <div key={stat.num} className="py-4">
                                <p
                                    className="font-black text-white tabular-nums"
                                    style={{
                                        fontSize: '3.5rem',
                                        letterSpacing: '-0.02em',
                                        lineHeight: 1.1,
                                    }}
                                >
                                    {stat.num}
                                </p>
                                <p className="mt-2 text-sm text-slate-400">
                                    {stat.label}
                                </p>
                                {stat.sub && (
                                    <p className="text-xs text-slate-600">
                                        {stat.sub}
                                    </p>
                                )}
                            </div>
                        ))}
                    </div>
                    <p className="mt-8 text-center text-sm text-slate-500">
                        Data ini adalah alasan Cekarah dibangun.
                    </p>
                </div>
            </section>

            {/* SECTION 5 — RESPONSIBLE AI */}
            <section id="responsible-ai" className="bg-white py-24">
                <div className="mx-auto max-w-6xl px-6">
                    <p className="text-xs tracking-widest text-slate-400 uppercase">
                        RESPONSIBLE AI
                    </p>
                    <h2
                        className="mt-2 text-4xl font-black text-slate-900"
                        style={{ letterSpacing: '-0.02em' }}
                    >
                        AI ini punya batas. Sengaja.
                    </h2>
                    <div className="mt-12 grid gap-12 lg:grid-cols-2">
                        <p className="leading-relaxed text-slate-600">
                            Cekarah adalah navigator awal, bukan otoritas final.
                            Setiap respons menyertakan rujukan sumber resmi dan
                            kontak petugas manusia. Jika tidak yakin, sistem
                            mendorong user untuk menghubungi petugas langsung —
                            bukan mencoba menjawab dengan tebakan.
                        </p>
                        <div className="space-y-4 text-sm">
                            {[
                                {
                                    label: 'Data sumber',
                                    value: 'Open data publik & sintetis — tidak ada data pribadi',
                                },
                                {
                                    label: 'Transparansi',
                                    value: 'Setiap jawaban menampilkan sumber dan tingkat keyakinan AI',
                                },
                                {
                                    label: 'Eskalasi',
                                    value: 'Kontak petugas resmi tersedia di setiap respons',
                                },
                            ].map((item) => (
                                <div key={item.label} className="flex gap-4">
                                    <span className="w-28 shrink-0 font-medium text-slate-500">
                                        {item.label}
                                    </span>
                                    <span className="text-slate-700">
                                        : {item.value}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* SECTION 6 — CTA PENUTUP */}
            <section
                id="cta"
                className="py-32 text-center"
                style={{ backgroundColor: '#0A0F1E' }}
            >
                <div className="mx-auto max-w-2xl px-6">
                    <h2
                        className="font-black text-white"
                        style={{
                            fontSize: 'clamp(3rem, 8vw, 4rem)',
                            letterSpacing: '-0.03em',
                        }}
                    >
                        Coba sekarang.
                    </h2>
                    <div className="mt-8">
                        <Link
                            href="/chat"
                            className="inline-block rounded-lg px-10 py-5 text-lg font-semibold text-white transition-colors"
                            style={{ backgroundColor: '#E63946' }}
                            onMouseOver={(e) =>
                                (e.currentTarget.style.backgroundColor =
                                    '#c1121f')
                            }
                            onMouseOut={(e) =>
                                (e.currentTarget.style.backgroundColor =
                                    '#E63946')
                            }
                        >
                            Mulai percakapan →
                        </Link>
                    </div>
                    <p className="mt-4 text-sm text-slate-500">
                        Tanpa akun. Tanpa data pribadi.
                    </p>
                </div>
            </section>

            {/* FOOTER */}
            <footer
                className="border-t border-slate-800 px-6 py-8"
                style={{ backgroundColor: '#060B14' }}
            >
                <div className="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 text-xs text-slate-600 lg:flex-row">
                    <div>
                        <span className="font-medium text-slate-400">
                            Cekarah
                        </span>
                        <span className="ml-2">· LKS Dikmen Nasional 2026</span>
                    </div>
                    <div className="flex gap-6">
                        <Link
                            href="/chat"
                            className="transition-colors hover:text-slate-400"
                        >
                            Chat
                        </Link>
                        <Link
                            href="/about"
                            className="transition-colors hover:text-slate-400"
                        >
                            Tentang
                        </Link>
                    </div>
                    <span>Dibuat dengan Laravel 13 + Gemini AI</span>
                </div>
            </footer>
        </div>
    );
}
