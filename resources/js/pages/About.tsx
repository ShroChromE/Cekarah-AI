import { Link } from '@inertiajs/react';

const SOURCES = [
    { name: 'BNPB', url: 'https://bnpb.go.id', category: 'Prosedur & Evakuasi', type: 'Sintetis (berdasarkan SOP resmi)' },
    { name: 'Basarnas', url: 'https://basarnas.go.id', category: 'Prosedur SAR', type: 'Sintetis (berdasarkan SOP resmi)' },
    { name: 'Kemensos', url: 'https://kemensos.go.id', category: 'Bantuan Sosial', type: 'Sintetis (berdasarkan panduan resmi)' },
    { name: 'PMI', url: 'https://pmi.or.id', category: 'Bantuan Darurat', type: 'Sintetis (berdasarkan data resmi)' },
    { name: 'BMKG', url: 'https://bmkg.go.id', category: 'Verifikasi Bencana', type: 'Sintetis (berdasarkan data resmi)' },
    { name: 'Kemkomdigi — Aduan Hoaks', url: 'https://aduankonten.id', category: 'Verifikasi Hoaks', type: 'Sintetis (berdasarkan panduan resmi)' },
];

export default function About() {
    return (
        <div className="min-h-screen bg-slate-50">
            <header className="flex items-center justify-between bg-slate-900 px-4 py-3 text-white">
                <div>
                    <h1 className="text-lg font-semibold tracking-tight">Cekarah</h1>
                    <p className="text-xs text-slate-400">Navigator bantuan & verifikasi informasi</p>
                </div>
                <Link
                    href="/chat"
                    className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                >
                    Buka chat
                </Link>
            </header>

            <main className="mx-auto max-w-2xl px-4 py-12">
                <h2 className="text-2xl font-bold text-slate-900">Tentang Cekarah</h2>
                <p className="mt-4 text-base leading-relaxed text-slate-600">
                    Cekarah adalah asisten AI berbasis chat yang membantu warga Indonesia menemukan bantuan
                    resmi dan memverifikasi informasi dalam situasi darurat bencana. Dibuat untuk kompetisi
                    LKS Dikmen Nasional 2026, Ekshibisi Kecerdasan Artifisial.
                </p>

                <div className="mt-10 border-t border-slate-200 pt-8">
                    <h3 className="text-base font-semibold text-slate-900">Cara kerja</h3>
                    <p className="mt-2 text-sm leading-relaxed text-slate-600">
                        AI mencari di dokumen resmi dulu, bukan mengarang. Setiap respons dihasilkan dari
                        pencarian di knowledge base yang berisi prosedur dan informasi dari lembaga resmi
                        (BNPB, Kemensos, PMI, BMKG). Sistem juga memeriksa apakah informasi masih aktual
                        dan menyertakan kontak petugas di setiap jawaban.
                    </p>
                    <ol className="mt-4 space-y-2 text-sm text-slate-600">
                        <li className="flex gap-3">
                            <span className="font-mono text-slate-400">01</span>
                            <span>AI mendeteksi kebutuhan: navigasi bantuan atau verifikasi informasi</span>
                        </li>
                        <li className="flex gap-3">
                            <span className="font-mono text-slate-400">02</span>
                            <span>Sistem mencari di knowledge base resmi menggunakan vector similarity</span>
                        </li>
                        <li className="flex gap-3">
                            <span className="font-mono text-slate-400">03</span>
                            <span>AI menyusun respons dengan sumber, tingkat keyakinan, dan kontak petugas</span>
                        </li>
                    </ol>
                </div>

                <div className="mt-10 border-t border-slate-200 pt-8">
                    <h3 className="text-base font-semibold text-slate-900">Sumber data</h3>
                    <p className="mt-1 text-sm text-slate-500">
                        Seluruh data bersifat sintetis berdasarkan informasi publik dari lembaga resmi.
                        Tidak ada data pribadi yang digunakan.
                    </p>
                    <div className="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">
                        {SOURCES.map((s) => (
                            <div key={s.name} className="flex items-start justify-between gap-4 px-4 py-3">
                                <div>
                                    <p className="text-sm font-medium text-slate-800">{s.name}</p>
                                    <p className="text-xs text-slate-500">{s.category} · {s.type}</p>
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
                </div>

                <div className="mt-10 rounded-lg border border-amber-200 bg-amber-50 px-4 py-4">
                    <h3 className="text-sm font-semibold text-amber-800">Batasan sistem</h3>
                    <ul className="mt-2 space-y-1 text-sm text-amber-700">
                        <li>· AI bisa salah — selalu verifikasi ke sumber dan petugas resmi</li>
                        <li>· Data knowledge base bisa usang — cek tanggal sumber yang ditampilkan</li>
                        <li>· Cekarah bukan pengganti petugas — hanya navigator awal</li>
                    </ul>
                </div>

                <div className="mt-10 border-t border-slate-200 pt-8">
                    <h3 className="text-sm font-semibold text-slate-900">Laporkan jawaban yang salah</h3>
                    <p className="mt-1 text-sm text-slate-600">
                        Temukan kesalahan? Hubungi tim melalui kanal kompetisi LKS Dikmen Nasional 2026
                        atau langsung ke penyelenggara ekshibisi.
                    </p>
                </div>
            </main>

            <footer className="border-t border-slate-200 bg-slate-900 px-4 py-6 text-center">
                <p className="text-xs text-slate-500">
                    Cekarah · LKS Dikmen Nasional 2026 · Dibuat dengan Laravel 13 + Gemini AI
                </p>
            </footer>
        </div>
    );
}
