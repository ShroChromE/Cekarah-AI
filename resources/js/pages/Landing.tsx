import BrandMark from '@/components/BrandMark';
import CountUp from '@/components/CountUp';
import Reveal from '@/components/Reveal';
import { Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

const HERO_STATS = [
    { value: 1199, label: 'korban meninggal, bencana Sumatera Nov 2025' },
    { value: 114200, label: 'warga mengungsi dalam 48 jam pertama' },
    { value: 1890, label: 'konten hoaks teridentifikasi (Okt 2024–Des 2025)' },
];

const FLOW_STEPS = [
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
];

const DATA_STATS = [
    {
        value: 390,
        label: 'laporan kesejahteraan sosial 2024',
        sub: '(Ombudsman)',
    },
    { value: 1890, label: 'konten hoaks teridentifikasi', sub: '(Kemkomdigi)' },
    { value: 5, label: 'orang ditangkap karena hoaks bencana Aceh', sub: '' },
];

const RED = '#E63946';
const RED_HOVER = '#c1121f';

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
            {/* Navbar */}
            <nav
                className={`fixed top-0 z-50 w-full px-6 py-3.5 transition-all duration-300 ${
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
                    <div className="flex items-center gap-2.5">
                        <BrandMark size={28} />
                        <span className="font-semibold tracking-tight text-white">
                            Cekarah
                        </span>
                    </div>
                    <div className="flex items-center gap-6">
                        <Link
                            href="/chat"
                            className="rounded-lg bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/15 backdrop-blur-sm transition-all hover:bg-white/20"
                        >
                            Buka Chat
                        </Link>
                    </div>
                </div>
            </nav>

            {/* HERO */}
            <section
                className="relative flex min-h-screen items-center overflow-hidden pt-16"
                style={{ backgroundColor: '#0A0F1E' }}
            >
                <div className="glow-red pointer-events-none absolute -top-1/4 left-1/4 h-[120%] w-[60%]" />
                <div className="relative mx-auto w-full max-w-6xl px-6 py-20">
                    <div className="flex flex-col gap-16 lg:flex-row lg:items-center">
                        {/* Left */}
                        <div className="lg:w-3/5">
                            <Reveal>
                                <p
                                    className="mb-6 text-xs font-medium text-slate-400"
                                    style={{ letterSpacing: '0.18em' }}
                                >
                                    EKSHIBISI KA — LKS DIKMEN NASIONAL 2026
                                </p>
                            </Reveal>
                            <Reveal delay={80}>
                                <h1
                                    className="heading-tight font-black text-white"
                                    style={{
                                        fontSize: 'clamp(2.5rem, 7vw, 4.75rem)',
                                    }}
                                >
                                    48 jam pertama
                                    <br />
                                    yang menentukan.
                                </h1>
                            </Reveal>
                            <Reveal delay={160}>
                                <p className="mt-6 max-w-md text-lg leading-relaxed text-slate-400">
                                    Cekarah membantu warga menemukan bantuan
                                    resmi dan memverifikasi informasi dalam
                                    situasi darurat bencana.
                                </p>
                            </Reveal>
                            <Reveal delay={240}>
                                <div className="mt-10 flex flex-wrap items-center gap-x-6 gap-y-4">
                                    <Link
                                        href="/chat"
                                        className="group inline-flex items-center gap-2 rounded-lg px-8 py-4 text-base font-semibold text-white shadow-lg shadow-red-900/30 transition-all hover:-translate-y-0.5"
                                        style={{ backgroundColor: RED }}
                                        onMouseOver={(e) =>
                                            (e.currentTarget.style.backgroundColor =
                                                RED_HOVER)
                                        }
                                        onMouseOut={(e) =>
                                            (e.currentTarget.style.backgroundColor =
                                                RED)
                                        }
                                    >
                                        Mulai sekarang
                                        <span className="transition-transform group-hover:translate-x-1">
                                            →
                                        </span>
                                    </Link>
                                </div>
                            </Reveal>
                        </div>

                        {/* Right — stat ticker */}
                        <div className="hidden lg:block lg:w-2/5">
                            <Reveal delay={300}>
                                <div className="space-y-0">
                                    {HERO_STATS.map((stat, i) => (
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
                                                className="heading-tight font-black text-white"
                                                style={{ fontSize: '3.5rem' }}
                                            >
                                                <CountUp value={stat.value} />
                                            </p>
                                            <p className="mt-1 text-sm text-slate-400">
                                                {stat.label}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            </Reveal>
                        </div>
                    </div>
                </div>

                {/* Scroll hint */}
                <div className="absolute bottom-8 left-1/2 -translate-x-1/2">
                    <div className="flex h-9 w-5 items-start justify-center rounded-full border border-slate-700 p-1.5">
                        <div className="h-1.5 w-1 animate-bounce rounded-full bg-slate-500" />
                    </div>
                </div>
            </section>

            {/* MASALAH */}
            <section className="bg-white py-24">
                <div className="mx-auto max-w-6xl px-6">
                    <Reveal>
                        <h2 className="max-w-2xl text-3xl font-bold tracking-tight text-slate-900">
                            Dalam 48 jam pertama krisis, warga menghadapi dua
                            masalah sekaligus.
                        </h2>
                    </Reveal>
                    <div className="mt-12 grid gap-10 lg:grid-cols-2">
                        <Reveal
                            delay={80}
                            className="border-l-4 border-slate-900 pl-6"
                        >
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
                        </Reveal>
                        <Reveal
                            delay={160}
                            className="border-l-4 border-l-[#E63946] pl-6"
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
                        </Reveal>
                    </div>
                </div>
            </section>

            {/* CARA KERJA */}
            <section className="py-24" style={{ backgroundColor: '#F0F4FF' }}>
                <div className="mx-auto max-w-6xl px-6">
                    <Reveal>
                        <p className="text-xs font-medium tracking-widest text-slate-400 uppercase">
                            Cara kerja
                        </p>
                        <h2 className="heading-tight mt-2 text-4xl font-black text-slate-900">
                            Satu kotak chat. Dua kemampuan.
                        </h2>
                    </Reveal>

                    <div className="mt-12 flex flex-col gap-8 lg:flex-row lg:items-start lg:gap-0">
                        {FLOW_STEPS.map((step, i) => (
                            <Reveal
                                key={i}
                                delay={i * 100}
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
                                {i < FLOW_STEPS.length - 1 && (
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
                            </Reveal>
                        ))}
                    </div>

                    <div className="mt-16 grid gap-8 lg:grid-cols-2">
                        <Reveal className="rounded-2xl border border-slate-200 bg-white p-7">
                            <h3 className="font-semibold text-slate-900">
                                Navigasi Bantuan
                            </h3>
                            <p className="mt-1 text-sm font-medium text-slate-500">
                                Temukan bantuan yang tepat
                            </p>
                            <ul className="mt-4 space-y-2.5 text-sm text-slate-600">
                                {[
                                    'Prosedur evakuasi banjir step-by-step',
                                    'Cara daftar bantuan sosial darurat (PKH, BPNT)',
                                    'Kontak resmi BNPB, Basarnas, PMI, Kemensos',
                                ].map((item) => (
                                    <li key={item} className="flex gap-2.5">
                                        <span className="text-blue-500">→</span>
                                        {item}
                                    </li>
                                ))}
                            </ul>
                        </Reveal>
                        <Reveal
                            delay={120}
                            className="rounded-2xl border border-slate-200 bg-white p-7"
                        >
                            <h3 className="font-semibold text-slate-900">
                                Verifikasi Klaim
                            </h3>
                            <p className="mt-1 text-sm font-medium text-slate-500">
                                Cek sebelum percaya
                            </p>
                            <ul className="mt-4 space-y-2.5 text-sm text-slate-600">
                                {[
                                    'Cross-check klaim dengan sumber BNPB & BMKG',
                                    'Penjelasan dengan alasan, bukan vonis "hoaks"',
                                    'Rujukan langsung ke sumber resmi',
                                ].map((item) => (
                                    <li key={item} className="flex gap-2.5">
                                        <span className="text-violet-500">
                                            →
                                        </span>
                                        {item}
                                    </li>
                                ))}
                            </ul>
                        </Reveal>
                    </div>
                </div>
            </section>

            {/* DATA */}
            <section className="py-24" style={{ backgroundColor: '#0F172A' }}>
                <div className="mx-auto max-w-6xl px-6">
                    <div className="grid grid-cols-1 gap-10 text-center lg:grid-cols-3">
                        {DATA_STATS.map((stat, i) => (
                            <Reveal
                                key={stat.label}
                                delay={i * 120}
                                className="py-4"
                            >
                                <p
                                    className="heading-tight font-black text-white"
                                    style={{ fontSize: '3.5rem' }}
                                >
                                    <CountUp value={stat.value} />
                                </p>
                                <p className="mt-2 text-sm text-slate-400">
                                    {stat.label}
                                </p>
                                {stat.sub && (
                                    <p className="text-xs text-slate-600">
                                        {stat.sub}
                                    </p>
                                )}
                            </Reveal>
                        ))}
                    </div>
                    <Reveal>
                        <p className="mt-10 text-center text-sm text-slate-500">
                            Data ini adalah alasan Cekarah dibangun.
                        </p>
                    </Reveal>
                </div>
            </section>

            {/* CTA */}
            <section className="relative bg-white py-32 text-center">
                <div className="relative mx-auto max-w-2xl px-6">
                    <Reveal>
                        <h2
                            className="heading-tight font-black text-slate-900"
                            style={{ fontSize: 'clamp(3rem, 8vw, 4rem)' }}
                        >
                            Coba sekarang.
                        </h2>
                    </Reveal>
                    <Reveal delay={120}>
                        <div className="mt-8">
                            <Link
                                href="/chat"
                                className="group inline-flex items-center gap-2 rounded-lg px-10 py-5 text-lg font-semibold text-white shadow-lg shadow-red-900/30 transition-all hover:-translate-y-0.5"
                                style={{ backgroundColor: RED }}
                                onMouseOver={(e) =>
                                    (e.currentTarget.style.backgroundColor =
                                        RED_HOVER)
                                }
                                onMouseOut={(e) =>
                                    (e.currentTarget.style.backgroundColor =
                                        RED)
                                }
                            >
                                Mulai percakapan
                                <span className="transition-transform group-hover:translate-x-1">
                                    →
                                </span>
                            </Link>
                        </div>
                        <p className="mt-4 text-sm text-slate-500">
                            Tanpa akun. Tanpa data pribadi.
                        </p>
                    </Reveal>
                </div>
            </section>

            {/* FOOTER */}
            <footer
                className="border-t border-slate-800 px-6 py-8"
                style={{ backgroundColor: '#060B14' }}
            >
                <div className="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 text-xs text-slate-600 lg:flex-row">
                    <div className="flex items-center gap-2.5">
                        <BrandMark size={22} />
                        <span className="font-medium text-slate-400">
                            Cekarah
                        </span>
                        <span>· LKS Dikmen Nasional 2026</span>
                    </div>
                    <div className="flex gap-6">
                        <Link
                            href="/chat"
                            className="transition-colors hover:text-slate-400"
                        >
                            Chat
                        </Link>
                    </div>
                    <span>Dibuat dengan Laravel 13 + Gemini AI</span>
                </div>
            </footer>
        </div>
    );
}
