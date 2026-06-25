import PortalShell from '@/components/portal/PortalShell';
import { Head, Link, router } from '@inertiajs/react';

type Claim = {
    id: number;
    claim_text: string;
    status: string;
    region: string | null;
    updated_by: number | null;
    updated_at: string | null;
    updated_by_user?: { name: string } | null;
};

const STATUS_STYLE: Record<string, string> = {
    verified: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    hoax: 'bg-red-50 text-red-700 border-red-200',
    unverified: 'bg-amber-50 text-amber-700 border-amber-200',
    no_official_data: 'bg-slate-100 text-slate-600 border-slate-200',
};

export default function ClaimsIndex({ claims }: { claims: (Claim & { updatedBy?: { name: string } | null })[] }) {
    const remove = (id: number) => {
        if (confirm('Hapus klaim ini?')) router.delete(`/portal/claims/${id}`);
    };

    return (
        <PortalShell
            title="Klaim Terverifikasi"
            actions={
                <Link href="/portal/claims/create" className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    + Tambah klaim
                </Link>
            }
        >
            <Head title="Klaim — Portal Relawan" />

            <div className="overflow-hidden rounded-xl border border-slate-200 bg-white">
                {claims.length === 0 ? (
                    <p className="px-5 py-12 text-center text-sm text-slate-400">Belum ada klaim. Tambahkan hasil cek fakta manual.</p>
                ) : (
                    <ul className="divide-y divide-slate-100">
                        {claims.map((c) => (
                            <li key={c.id} className="flex items-start justify-between gap-4 px-5 py-3.5">
                                <div className="min-w-0">
                                    <div className="flex items-center gap-2">
                                        <span className={`rounded border px-1.5 py-0.5 text-xs font-medium ${STATUS_STYLE[c.status] ?? ''}`}>{c.status}</span>
                                        {c.region && <span className="text-xs text-slate-400">{c.region}</span>}
                                    </div>
                                    <p className="mt-1 line-clamp-2 text-sm text-slate-800">{c.claim_text}</p>
                                    {c.updatedBy && <p className="mt-0.5 text-xs text-slate-400">Terakhir diubah oleh {c.updatedBy.name}</p>}
                                </div>
                                <div className="flex shrink-0 items-center gap-2">
                                    <Link href={`/portal/claims/${c.id}/edit`} className="text-xs text-blue-600 hover:underline">
                                        Edit
                                    </Link>
                                    <button onClick={() => remove(c.id)} className="text-xs text-red-500 hover:underline">
                                        Hapus
                                    </button>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </PortalShell>
    );
}
