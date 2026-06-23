import BrandMark from '@/components/BrandMark';
import Reveal from '@/components/Reveal';
import { Link } from '@inertiajs/react';

const SOURCES = [
    {
        name: 'BNPB',
        url: 'https://bnpb.go.id',
        category: 'Prosedur & Evakuasi',
        type: 'Sintetis (berdasarkan SOP resmi)',
    },
    {
        name: 'Basarnas',
        url: 'https://basarnas.go.id',
        category: 'Prosedur SAR',
        type: 'Sintetis (berdasarkan SOP resmi)',
    },
    {
        name: 'Kemensos',
        url: 'https://kemensos.go.id',
        category: 'Bantuan Sosial',
        type: 'Sintetis (berdasarkan panduan resmi)',
    },
    {
        name: 'PMI',
        url: 'https://pmi.or.id',
        category: 'Bantuan Darurat',
        type: 'Sintetis (berdasarkan data resmi)',
    },
    {
        name: 'BMKG',
        url: 'https://bmkg.go.id',
        category: 'Verifikasi Bencana',
        type: 'Sintetis (berdasarkan data resmi)',
    },
    {
        name: 'Kemkomdigi — Aduan Hoaks',
        url: 'https://aduankonten.id',
        category: 'Verifikasi Hoaks',
        type: 'Sintetis (berdasarkan panduan resmi)',
    },
];

const STEPS = [
    'AI mendeteksi kebutuhan: navigasi bantuan atau verifikasi informasi',
    'Sistem mencari di knowledge base resmi menggunakan vector similarity',
    'AI menyusun respons dengan sumber, tingkat keyakinan, dan kontak petugas',
];

export default function About() {
    return (
        <div className="min-h-screen bg-white">
            {/* Header */}
            <header className="sticky top-0 z-10 border-b border-slate-800/60 bg-[#0A0F1E]">
                <div className="mx-auto flex h-14 max-w-3xl items-center justify-between px-4">
                    <Link href="/" className="flex items-center gap-2.5">
                        <BrandMark size={26} />
                        <span className="font-bold tracking-tight text-white">
                            Cekarah
                        </span>
                    </Link>
                    <Link
                        href="/chat"
                        className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                    >
                        Buka chat
                    </Link>
                </div>
            </header>

            <main className="mx-auto max-w-3xl px-4 py-14">
                <Reveal>
                    <p className="text-xs font-medium tracking-widest text-slate-400 uppercase">
                        Tentang sistem
                    </p>
                    <h1 className="heading-tight mt-2 text-3xl font-black text-slate-900">
                        Apa itu Cekarah?
                    </h1>
                    <p className="mt-4 text-base leading-relaxed text-slate-600">
                        Cekarah adalah asisten AI berbasis chat yang membantu
                        warga Indonesia menemukan bantuan resmi dan
                        memverifikasi informasi dalam situasi darurat bencana.
                        Dibuat untuk kompetisi LKS Dikmen Nasional 2026,
                        Ekshibisi Kecerdasan Artifisial.
                    </p>
                </Reveal>

                <Reveal className="mt-12 border-t border-slate-100 pt-8">
                    <h2 className="text-base font-semibold text-slate-900">
                        Cara kerja
                    </h2>
                    <p className="mt-2 text-sm leading-relaxed text-slate-600">
                        AI mencari di dokumen resmi dulu, bukan mengarang.
                        Setiap respons dihasilkan dari pencarian di knowledge
                        base yang berisi prosedur dan informasi dari lembaga
                        resmi (BNPB, Kemensos, PMI, BMKG). Sistem juga memeriksa
                        apakah informasi masih aktual dan menyertakan kontak
                        petugas di setiap jawaban.
                    </p>
                    <ol className="mt-5 space-y-3 text-sm text-slate-600">
                        {STEPS.map((step, i) => (
                            <li key={i} className="flex gap-3">
                                <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-100 font-mono text-xs text-slate-500">
                                    {String(i + 1).padStart(2, '0')}
                                </span>
                                <span className="pt-0.5">{step}</span>
                            </li>
                        ))}
                    </ol>
                </Reveal>

                <Reveal className="mt-12 border-t border-slate-100 pt-8">
                    <h2 className="text-base font-semibold text-slate-900">
                        Sumber data
                    </h2>
                    <p className="mt-1 text-sm text-slate-500">
                        Seluruh data bersifat sintetis berdasarkan informasi
                        publik dari lembaga resmi. Tidak ada data pribadi yang
                        digunakan.
                    </p>
                    <div className="mt-4 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white">
                        {SOURCES.map((s) => (
                            <div
                                key={s.name}
                                className="flex items-start justify-between gap-4 px-4 py-3.5 transition-colors hover:bg-slate-50"
                            >
                                <div>
                                    <p className="text-sm font-medium text-slate-800">
                                        {s.name}
                                    </p>
                                    <p className="text-xs text-slate-500">
                                        {s.category} · {s.type}
                                    </p>
                                </div>
                                <a
                                    href={s.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="shrink-0 text-xs text-blue-600 hover:underline"
                                >
                                    {s.url.replace('https://', '')}
                                </a>
                            </div>
                        ))}
                    </div>
                </Reveal>

                <Reveal className="mt-12 rounded-xl border border-amber-200 bg-amber-50 px-5 py-5">
                    <h2 className="text-sm font-semibold text-amber-800">
                        Batasan sistem
                    </h2>
                    <ul className="mt-2 space-y-1.5 text-sm text-amber-700">
                        <li>
                            · AI bisa salah — selalu verifikasi ke sumber dan
                            petugas resmi
                        </li>
                        <li>
                            · Data knowledge base bisa usang — cek tanggal
                            sumber yang ditampilkan
                        </li>
                        <li>
                            · Cekarah bukan pengganti petugas — hanya navigator
                            awal
                        </li>
                    </ul>
                </Reveal>

                <Reveal className="mt-12 border-t border-slate-100 pt-8">
                    <h2 className="text-sm font-semibold text-slate-900">
                        Laporkan jawaban yang salah
                    </h2>
                    <p className="mt-1 text-sm text-slate-600">
                        Temukan kesalahan? Hubungi tim melalui kanal kompetisi
                        LKS Dikmen Nasional 2026 atau langsung ke penyelenggara
                        ekshibisi.
                    </p>
                </Reveal>
            </main>

            <footer
                className="border-t border-slate-800 px-4 py-6 text-center"
                style={{ backgroundColor: '#0A0F1E' }}
            >
                <p className="text-xs text-slate-500">
                    Cekarah · LKS Dikmen Nasional 2026 · Dibuat dengan Laravel
                    13 + Gemini AI
                </p>
            </footer>
        </div>
    );
}
