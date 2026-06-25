import CoordinatePicker from '@/components/portal/CoordinatePicker';
import { Area, Select, Text } from '@/components/portal/fields';
import PortalShell from '@/components/portal/PortalShell';
import { Head, useForm } from '@inertiajs/react';

type SourceRow = { name: string; url: string | null; published_at: string | null };
type Shelter = {
    id: number;
    name: string;
    type: string;
    address: string;
    region: string;
    latitude: number;
    longitude: number;
    capacity: number | null;
    occupancy: number | null;
    contact: string | null;
    notes: string | null;
    disaster_event_id: number | null;
    is_active: boolean;
    sources?: SourceRow[];
};
type EventOpt = { id: number; name: string };

const TYPES = [
    { value: 'evacuation_shelter', label: 'Posko Pengungsian' },
    { value: 'public_kitchen', label: 'Dapur Umum' },
    { value: 'health_post', label: 'Pos Kesehatan' },
    { value: 'logistics_post', label: 'Pos Logistik' },
];

export default function ShelterForm({ shelter, events }: { shelter?: Shelter; events: EventOpt[] }) {
    const editing = !!shelter;
    const src = shelter?.sources?.[0];

    const form = useForm({
        name: shelter?.name ?? '',
        type: shelter?.type ?? 'evacuation_shelter',
        address: shelter?.address ?? '',
        region: shelter?.region ?? '',
        latitude: shelter?.latitude ?? ('' as number | ''),
        longitude: shelter?.longitude ?? ('' as number | ''),
        capacity: shelter?.capacity ?? ('' as number | ''),
        occupancy: shelter?.occupancy ?? ('' as number | ''),
        contact: shelter?.contact ?? '',
        notes: shelter?.notes ?? '',
        disaster_event_id: shelter?.disaster_event_id ?? '',
        is_active: shelter?.is_active ?? true,
        source_name: src?.name ?? '',
        source_url: src?.url ?? '',
        source_date: src?.published_at ?? '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editing) form.put(`/portal/shelters/${shelter!.id}`);
        else form.post('/portal/shelters');
    };

    return (
        <PortalShell title={editing ? 'Edit Posko' : 'Tambah Posko / Shelter'}>
            <Head title="Posko — Portal Relawan" />

            <form onSubmit={submit} className="max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-white p-6">
                <Text label="Nama posko" required value={form.data.name} onChange={(v) => form.setData('name', v)} error={form.errors.name} />
                <div className="grid gap-5 sm:grid-cols-2">
                    <Select label="Jenis" required value={form.data.type} onChange={(v) => form.setData('type', v)} options={TYPES} error={form.errors.type} />
                    <Text label="Wilayah" required value={form.data.region} onChange={(v) => form.setData('region', v)} error={form.errors.region} placeholder="mis. Binjai" />
                </div>
                <Area label="Alamat" required value={form.data.address} onChange={(v) => form.setData('address', v)} error={form.errors.address} />

                <div>
                    <p className="mb-1 text-sm font-medium text-slate-700">Koordinat <span className="text-red-500">*</span></p>
                    <CoordinatePicker
                        lat={form.data.latitude === '' ? null : Number(form.data.latitude)}
                        lng={form.data.longitude === '' ? null : Number(form.data.longitude)}
                        onPick={(la, ln) => {
                            form.setData('latitude', la);
                            form.setData('longitude', ln);
                        }}
                    />
                    <div className="mt-3 grid gap-4 sm:grid-cols-2">
                        <Text label="Latitude" required type="number" value={form.data.latitude} onChange={(v) => form.setData('latitude', v === '' ? '' : Number(v))} error={form.errors.latitude} />
                        <Text label="Longitude" required type="number" value={form.data.longitude} onChange={(v) => form.setData('longitude', v === '' ? '' : Number(v))} error={form.errors.longitude} />
                    </div>
                </div>

                <div className="grid gap-5 sm:grid-cols-2">
                    <Text label="Kapasitas (opsional)" type="number" value={form.data.capacity} onChange={(v) => form.setData('capacity', v === '' ? '' : Number(v))} error={form.errors.capacity} />
                    <Text label="Terisi (opsional)" type="number" value={form.data.occupancy} onChange={(v) => form.setData('occupancy', v === '' ? '' : Number(v))} error={form.errors.occupancy} />
                </div>
                <Text label="Kontak (opsional)" value={form.data.contact} onChange={(v) => form.setData('contact', v)} error={form.errors.contact} />
                <Area label="Catatan (opsional)" value={form.data.notes} onChange={(v) => form.setData('notes', v)} error={form.errors.notes} />

                <Select
                    label="Kaitkan ke peristiwa bencana (opsional)"
                    value={form.data.disaster_event_id}
                    onChange={(v) => form.setData('disaster_event_id', v)}
                    options={[{ value: '', label: '— tidak ada —' }, ...events.map((e) => ({ value: e.id, label: e.name }))]}
                />

                <div className="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p className="mb-3 text-sm font-medium text-slate-700">Sumber rujukan (opsional)</p>
                    <div className="space-y-4">
                        <Text label="Nama sumber" value={form.data.source_name} onChange={(v) => form.setData('source_name', v)} placeholder="mis. BPBD Binjai" />
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
                    <a href="/portal/shelters" className="text-sm text-slate-500 hover:underline">
                        Batal
                    </a>
                </div>
            </form>
        </PortalShell>
    );
}
