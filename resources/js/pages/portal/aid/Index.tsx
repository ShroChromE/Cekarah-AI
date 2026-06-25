import PortalShell from '@/components/portal/PortalShell';
import { Head, Link, router } from '@inertiajs/react';

type Program = {
    id: number;
    name: string;
    provider: string;
    aid_type: string;
    region: string;
    schedule_status: string;
    updatedBy?: { name: string } | null;
};

export default function AidIndex({ programs }: { programs: Program[] }) {
    const remove = (id: number) => {
        if (confirm('Hapus program bantuan ini?')) router.delete(`/portal/aid/${id}`);
    };

    return (
        <PortalShell
            title="Program Bantuan"
            actions={
                <Link href="/portal/aid/create" className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    + Tambah program
                </Link>
            }
        >
            <Head title="Bantuan — Portal Relawan" />
            <div className="overflow-hidden rounded-xl border border-slate-200 bg-white">
                {programs.length === 0 ? (
                    <p className="px-5 py-12 text-center text-sm text-slate-400">Belum ada program bantuan.</p>
                ) : (
                    <ul className="divide-y divide-slate-100">
                        {programs.map((p) => (
                            <li key={p.id} className="flex items-start justify-between gap-4 px-5 py-3.5">
                                <div className="min-w-0">
                                    <p className="text-sm font-medium text-slate-800">{p.name}</p>
                                    <p className="mt-0.5 text-xs text-slate-500">
                                        {p.provider} · {p.aid_type} · {p.region}
                                    </p>
                                    <span className="mt-1 inline-block rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-xs text-slate-500">
                                        {p.schedule_status}
                                    </span>
                                </div>
                                <div className="flex shrink-0 items-center gap-2">
                                    <Link href={`/portal/aid/${p.id}/edit`} className="text-xs text-blue-600 hover:underline">
                                        Edit
                                    </Link>
                                    <button onClick={() => remove(p.id)} className="text-xs text-red-500 hover:underline">
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
