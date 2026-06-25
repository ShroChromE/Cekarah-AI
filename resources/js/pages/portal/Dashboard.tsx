import PortalShell from '@/components/portal/PortalShell';
import { Head, Link } from '@inertiajs/react';

type Review = { id: number; user_message: string; detected_intent: string };

type Props = {
    stats: { shelters: number; aid_programs: number; claims: number; pending_reviews: number };
    recentReviews: Review[];
};

const CARDS = [
    { key: 'shelters', label: 'Posko & Shelter', href: '/portal/shelters' },
    { key: 'aid_programs', label: 'Program Bantuan', href: '/portal/aid' },
    { key: 'claims', label: 'Klaim Terverifikasi', href: '/portal/claims' },
    { key: 'pending_reviews', label: 'Perlu Ditinjau', href: '/portal/review' },
] as const;

export default function Dashboard({ stats, recentReviews }: Props) {
    return (
        <PortalShell title="Dashboard">
            <Head title="Dashboard — Portal Relawan" />

            <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
                {CARDS.map((c) => (
                    <Link
                        key={c.key}
                        href={c.href}
                        className="rounded-xl border border-slate-200 bg-white p-5 transition-all hover:-translate-y-0.5 hover:shadow-sm"
                    >
                        <p className="text-3xl font-bold tabular-nums text-slate-900">{stats[c.key]}</p>
                        <p className="mt-1 text-sm text-slate-500">{c.label}</p>
                    </Link>
                ))}
            </div>

            <div className="mt-8 rounded-xl border border-slate-200 bg-white">
                <div className="flex items-center justify-between border-b border-slate-100 px-5 py-3.5">
                    <h2 className="text-sm font-semibold text-slate-800">Pertanyaan terbaru yang perlu ditinjau</h2>
                    <Link href="/portal/review" className="text-xs text-blue-600 hover:underline">
                        Lihat semua →
                    </Link>
                </div>
                {recentReviews.length === 0 ? (
                    <p className="px-5 py-8 text-center text-sm text-slate-400">
                        Belum ada. Pertanyaan warga yang tidak menemukan data resmi akan muncul di sini.
                    </p>
                ) : (
                    <ul className="divide-y divide-slate-100">
                        {recentReviews.map((r) => (
                            <li key={r.id} className="flex items-center justify-between gap-4 px-5 py-3">
                                <div className="min-w-0">
                                    <p className="truncate text-sm text-slate-800">{r.user_message}</p>
                                    <p className="text-xs text-slate-400">{r.detected_intent}</p>
                                </div>
                                <Link
                                    href={`/portal/claims/create?claim=${encodeURIComponent(r.user_message)}`}
                                    className="shrink-0 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700"
                                >
                                    Tambah data
                                </Link>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </PortalShell>
    );
}
