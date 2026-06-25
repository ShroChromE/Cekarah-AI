import PortalShell from '@/components/portal/PortalShell';
import { Head, router } from '@inertiajs/react';

type Point = { date: string; count: number };
type Trend = {
    label: string;
    total: number;
    current: number;
    previous: number;
    trend_pct: number | null;
    is_surging: boolean;
    series: Point[];
};

type Props = {
    filters: { days: number; source: string };
    meta: { live: number; simulated: number; window_days: number };
    claimTrends: Trend[];
    regionNeeds: Trend[];
};

const DAY_OPTIONS = [7, 14, 30];
const SOURCE_OPTIONS: { value: string; label: string }[] = [
    { value: 'all', label: 'Semua' },
    { value: 'live', label: 'Live' },
    { value: 'simulated', label: 'Simulasi' },
];

function reload(filters: { days: number; source: string }) {
    router.get('/portal/radar', filters, { preserveScroll: true, preserveState: true });
}

function Bars({ series, surging }: { series: Point[]; surging: boolean }) {
    const max = Math.max(1, ...series.map((p) => p.count));
    return (
        <div className="flex h-12 items-end gap-0.5">
            {series.map((p) => (
                <div
                    key={p.date}
                    title={`${p.date}: ${p.count}`}
                    className={`flex-1 rounded-sm ${surging ? 'bg-red-400' : 'bg-blue-300'}`}
                    style={{ height: `${Math.max(6, (p.count / max) * 100)}%` }}
                />
            ))}
        </div>
    );
}

function TrendCard({ item }: { item: Trend }) {
    return (
        <div className="rounded-xl border border-slate-200 bg-white p-4">
            <div className="mb-2 flex items-start justify-between gap-3">
                <p className="text-sm font-medium text-slate-800">{item.label}</p>
                {item.is_surging && (
                    <span className="shrink-0 rounded-full bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-600">
                        Perlu perhatian
                    </span>
                )}
            </div>
            <Bars series={item.series} surging={item.is_surging} />
            <div className="mt-2 flex items-center justify-between text-xs text-slate-500">
                <span>{item.total} pertanyaan</span>
                <span>
                    {item.trend_pct === null ? 'tren baru' : `${item.trend_pct >= 0 ? '+' : ''}${item.trend_pct}% vs periode awal`}
                </span>
            </div>
        </div>
    );
}

function Section({ title, hint, items, empty }: { title: string; hint: string; items: Trend[]; empty: string }) {
    const surging = items.filter((i) => i.is_surging);
    return (
        <section className="mt-8">
            <h2 className="text-sm font-semibold text-slate-800">{title}</h2>
            <p className="mt-0.5 mb-3 text-xs text-slate-500">{hint}</p>

            {surging.length > 0 && (
                <div className="mb-3 flex flex-wrap gap-2">
                    {surging.map((i) => (
                        <span key={i.label} className="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs text-red-700">
                            ↑ {i.label}
                        </span>
                    ))}
                </div>
            )}

            {items.length === 0 ? (
                <p className="rounded-xl border border-slate-200 bg-white px-5 py-10 text-center text-sm text-slate-400">{empty}</p>
            ) : (
                <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    {items.map((i) => (
                        <TrendCard key={i.label} item={i} />
                    ))}
                </div>
            )}
        </section>
    );
}

export default function Radar({ filters, meta, claimTrends, regionNeeds }: Props) {
    return (
        <PortalShell title="Radar Tren">
            <Head title="Radar Tren — Portal Relawan" />

            <div className="-mt-2 mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <p className="max-w-2xl text-sm text-slate-500">
                    Sinyal agregat dari interaksi chat warga — bukan pengukuran resmi penyebaran hoaks. Anggap ini petunjuk
                    untuk <span className="font-medium text-slate-700">ditindaklanjuti manusia</span>, bukan kepastian statistik.
                </p>
                <div className="flex items-center gap-2">
                    <select
                        value={filters.days}
                        onChange={(e) => reload({ ...filters, days: Number(e.target.value) })}
                        className="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-sm text-slate-700"
                    >
                        {DAY_OPTIONS.map((d) => (
                            <option key={d} value={d}>
                                {d} hari
                            </option>
                        ))}
                    </select>
                    <select
                        value={filters.source}
                        onChange={(e) => reload({ ...filters, source: e.target.value })}
                        className="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-sm text-slate-700"
                    >
                        {SOURCE_OPTIONS.map((o) => (
                            <option key={o.value} value={o.value}>
                                {o.label}
                            </option>
                        ))}
                    </select>
                </div>
            </div>

            <div className="rounded-lg border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs text-amber-800">
                Jendela {meta.window_days} hari · {meta.live} interaksi live
                {meta.simulated > 0 && (
                    <>
                        {' '}· <span className="font-semibold">{meta.simulated} baris data simulasi/demo</span> (ditandai
                        khusus, bukan pengguna riil)
                    </>
                )}
                .
            </div>

            <Section
                title="Tren Klaim Hoaks"
                hint="Pertanyaan verifikasi klaim yang dikelompokkan berdasarkan kemiripan (bukan kata-per-kata identik)."
                items={claimTrends}
                empty="Belum ada pertanyaan verifikasi klaim pada periode ini."
            />

            <Section
                title="Tren Kebutuhan per Wilayah"
                hint="Pertanyaan posko & bantuan sosial yang dikelompokkan berdasarkan wilayah yang disebut."
                items={regionNeeds}
                empty="Belum ada pertanyaan posko/bantuan dengan wilayah yang dikenali pada periode ini."
            />
        </PortalShell>
    );
}
