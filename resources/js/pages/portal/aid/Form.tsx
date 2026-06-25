import { Area, Select, Text } from '@/components/portal/fields';
import PortalShell from '@/components/portal/PortalShell';
import { Head, useForm } from '@inertiajs/react';

type SourceRow = { name: string; url: string | null; published_at: string | null };
type Program = {
    id: number;
    name: string;
    provider: string;
    aid_type: string;
    description: string;
    region: string;
    eligibility: string | null;
    schedule_status: string;
    starts_at: string | null;
    ends_at: string | null;
    disaster_event_id: number | null;
    is_active: boolean;
    sources?: SourceRow[];
};
type EventOpt = { id: number; name: string };

const TYPES = [
    { value: 'cash', label: 'Tunai (cash)' },
    { value: 'food', label: 'Pangan (food)' },
    { value: 'logistics', label: 'Logistik (logistics)' },
    { value: 'health', label: 'Kesehatan (health)' },
    { value: 'shelter', label: 'Hunian (shelter)' },
];

const SCHEDULE = [
    { value: 'planned', label: 'Direncanakan (planned)' },
    { value: 'ongoing', label: 'Berjalan (ongoing)' },
    { value: 'distributed', label: 'Tersalurkan (distributed)' },
    { value: 'closed', label: 'Ditutup (closed)' },
];

export default function AidForm({ program, events }: { program?: Program; events: EventOpt[] }) {
    const editing = !!program;
    const src = program?.sources?.[0];

    const form = useForm({
        name: program?.name ?? '',
        provider: program?.provider ?? '',
        aid_type: program?.aid_type ?? 'logistics',
        description: program?.description ?? '',
        region: program?.region ?? '',
        eligibility: program?.eligibility ?? '',
        schedule_status: program?.schedule_status ?? 'planned',
        starts_at: program?.starts_at ?? '',
        ends_at: program?.ends_at ?? '',
        disaster_event_id: program?.disaster_event_id ?? '',
        is_active: program?.is_active ?? true,
        source_name: src?.name ?? '',
        source_url: src?.url ?? '',
        source_date: src?.published_at ?? '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editing) form.put(`/portal/aid/${program!.id}`);
        else form.post('/portal/aid');
    };

    return (
        <PortalShell title={editing ? 'Edit Program Bantuan' : 'Tambah Program Bantuan'}>
            <Head title="Bantuan — Portal Relawan" />

            <form onSubmit={submit} className="max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-white p-6">
                <Text label="Nama program" required value={form.data.name} onChange={(v) => form.setData('name', v)} error={form.errors.name} />
                <div className="grid gap-5 sm:grid-cols-2">
                    <Text label="Penyedia" required value={form.data.provider} onChange={(v) => form.setData('provider', v)} error={form.errors.provider} placeholder="mis. Kemensos" />
                    <Select label="Jenis bantuan" required value={form.data.aid_type} onChange={(v) => form.setData('aid_type', v)} options={TYPES} error={form.errors.aid_type} />
                </div>
                <Area label="Deskripsi" required value={form.data.description} onChange={(v) => form.setData('description', v)} error={form.errors.description} />
                <div className="grid gap-5 sm:grid-cols-2">
                    <Text label="Wilayah" required value={form.data.region} onChange={(v) => form.setData('region', v)} error={form.errors.region} placeholder="mis. Binjai" />
                    <Select label="Status jadwal" required value={form.data.schedule_status} onChange={(v) => form.setData('schedule_status', v)} options={SCHEDULE} error={form.errors.schedule_status} />
                </div>
                <Area label="Syarat singkat (opsional)" value={form.data.eligibility} onChange={(v) => form.setData('eligibility', v)} error={form.errors.eligibility} />
                <div className="grid gap-5 sm:grid-cols-2">
                    <Text label="Mulai (opsional)" type="date" value={form.data.starts_at} onChange={(v) => form.setData('starts_at', v)} error={form.errors.starts_at} />
                    <Text label="Selesai (opsional)" type="date" value={form.data.ends_at} onChange={(v) => form.setData('ends_at', v)} error={form.errors.ends_at} />
                </div>

                <Select
                    label="Kaitkan ke peristiwa bencana (opsional)"
                    value={form.data.disaster_event_id}
                    onChange={(v) => form.setData('disaster_event_id', v)}
                    options={[{ value: '', label: '— tidak ada —' }, ...events.map((e) => ({ value: e.id, label: e.name }))]}
                />

                <div className="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p className="mb-3 text-sm font-medium text-slate-700">Sumber rujukan (opsional)</p>
                    <div className="space-y-4">
                        <Text label="Nama sumber" value={form.data.source_name} onChange={(v) => form.setData('source_name', v)} placeholder="mis. Kemensos" />
                        <div className="grid gap-4 sm:grid-cols-2">
                            <Text label="URL sumber" type="url" value={form.data.source_url} onChange={(v) => form.setData('source_url', v)} error={form.errors.source_url} />
                            <Text label="Tanggal sumber" type="date" value={form.data.source_date} onChange={(v) => form.setData('source_date', v)} />
                        </div>
                    </div>
                </div>

                <label className="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" checked={form.data.is_active} onChange={(e) => form.setData('is_active', e.target.checked)} className="rounded border-slate-300" />
                    Aktif
                </label>

                <div className="flex items-center gap-3 pt-2">
                    <button type="submit" disabled={form.processing} className="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                        {form.processing ? 'Menyimpan…' : editing ? 'Simpan perubahan' : 'Simpan'}
                    </button>
                    <a href="/portal/aid" className="text-sm text-slate-500 hover:underline">
                        Batal
                    </a>
                </div>
            </form>
        </PortalShell>
    );
}
