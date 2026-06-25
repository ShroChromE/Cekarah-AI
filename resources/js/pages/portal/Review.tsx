import PortalShell from '@/components/portal/PortalShell';
import { Head, Link, router } from '@inertiajs/react';

type Item = { id: number; user_message: string; detected_intent: string; created_at: string | null };

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

const INTENT_LABEL: Record<string, string> = {
    disaster_info: 'Informasi bencana',
    claim_verification: 'Verifikasi klaim',
    shelter_location: 'Lokasi posko',
    aid_assistance: 'Bantuan sosial',
    out_of_scope: 'Di luar topik',
};

// Where "Tambah data resmi" should send the volunteer, per detected intent.
function targetFor(item: Item): string {
    const q = encodeURIComponent(item.user_message);
    switch (item.detected_intent) {
        case 'shelter_location':
            return '/portal/shelters/create';
        case 'aid_assistance':
            return '/portal/aid/create';
        default:
            return `/portal/claims/create?claim=${q}`;
    }
}

export default function Review({ items }: { items: Paginated<Item> }) {
    return (
        <PortalShell title="Perlu Ditinjau">
            <Head title="Perlu Ditinjau — Portal Relawan" />

            <p className="-mt-2 mb-5 max-w-2xl text-sm text-slate-500">
                Pertanyaan warga di mana sistem tidak menemukan data resmi (DB maupun web). Tambahkan data resminya agar
                jawaban chat publik membaik.
            </p>

            <div className="overflow-hidden rounded-xl border border-slate-200 bg-white">
                {items.data.length === 0 ? (
                    <p className="px-5 py-12 text-center text-sm text-slate-400">Tidak ada item yang perlu ditinjau. 🎉</p>
                ) : (
                    <ul className="divide-y divide-slate-100">
                        {items.data.map((item) => (
                            <li key={item.id} className="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <div className="min-w-0">
                                    <p className="text-sm text-slate-800">{item.user_message}</p>
                                    <p className="mt-0.5 text-xs text-slate-400">
                                        {INTENT_LABEL[item.detected_intent] ?? item.detected_intent}
                                        {item.created_at ? ` · ${new Date(item.created_at).toLocaleString('id-ID')}` : ''}
                                    </p>
                                </div>
                                <div className="flex shrink-0 items-center gap-2">
                                    <Link
                                        href={targetFor(item)}
                                        className="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700"
                                    >
                                        Tambah data resmi
                                    </Link>
                                    <button
                                        onClick={() => router.patch(`/portal/review/${item.id}/resolve`)}
                                        className="rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50"
                                    >
                                        Tandai selesai
                                    </button>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </div>

            {items.links.length > 3 && (
                <div className="mt-4 flex flex-wrap gap-1">
                    {items.links.map((link, i) => (
                        <Link
                            key={i}
                            href={link.url ?? '#'}
                            className={`rounded px-3 py-1.5 text-sm ${
                                link.active ? 'bg-blue-600 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                            } ${!link.url ? 'pointer-events-none opacity-40' : ''}`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>
            )}
        </PortalShell>
    );
}
