import PortalShell from '@/components/portal/PortalShell';
import { Head, Link, router } from '@inertiajs/react';

type Shelter = {
    id: number;
    name: string;
    type: string;
    region: string;
    address: string;
    disasterEvent?: { name: string } | null;
    updatedBy?: { name: string } | null;
};

const TYPE_LABEL: Record<string, string> = {
    evacuation_shelter: 'Posko Pengungsian',
    public_kitchen: 'Dapur Umum',
    health_post: 'Pos Kesehatan',
    logistics_post: 'Pos Logistik',
};

export default function SheltersIndex({ shelters }: { shelters: Shelter[] }) {
    const remove = (id: number) => {
        if (confirm('Hapus posko ini?')) router.delete(`/portal/shelters/${id}`);
    };

    return (
        <PortalShell
            title="Posko & Shelter"
            actions={
                <Link href="/portal/shelters/create" className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    + Tambah posko
                </Link>
            }
        >
            <Head title="Posko — Portal Relawan" />
            <div className="overflow-hidden rounded-xl border border-slate-200 bg-white">
                {shelters.length === 0 ? (
                    <p className="px-5 py-12 text-center text-sm text-slate-400">Belum ada posko.</p>
                ) : (
                    <ul className="divide-y divide-slate-100">
                        {shelters.map((s) => (
                            <li key={s.id} className="flex items-start justify-between gap-4 px-5 py-3.5">
                                <div className="min-w-0">
                                    <p className="text-sm font-medium text-slate-800">{s.name}</p>
                                    <p className="mt-0.5 text-xs text-slate-500">
                                        {TYPE_LABEL[s.type] ?? s.type} · {s.region}
                                    </p>
                                    <p className="truncate text-xs text-slate-400">{s.address}</p>
                                </div>
                                <div className="flex shrink-0 items-center gap-2">
                                    <Link href={`/portal/shelters/${s.id}/edit`} className="text-xs text-blue-600 hover:underline">
                                        Edit
                                    </Link>
                                    <button onClick={() => remove(s.id)} className="text-xs text-red-500 hover:underline">
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
