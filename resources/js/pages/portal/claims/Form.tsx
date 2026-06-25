import { Area, Select, Text } from '@/components/portal/fields';
import PortalShell from '@/components/portal/PortalShell';
import { Head, useForm } from '@inertiajs/react';

type SourceRow = { name: string; url: string | null; published_at: string | null };
type Claim = {
    id: number;
    claim_text: string;
    status: string;
    explanation: string;
    region: string | null;
    disaster_event_id: number | null;
    is_active: boolean;
    sources?: SourceRow[];
};
type EventOpt = { id: number; name: string };

const STATUS = [
    { value: 'verified', label: 'Terverifikasi benar (verified)' },
    { value: 'unverified', label: 'Belum terverifikasi (unverified)' },
    { value: 'hoax', label: 'Hoaks (hoax)' },
    { value: 'no_official_data', label: 'Belum ada data resmi (no_official_data)' },
];

export default function ClaimForm({ claim, events, prefill }: { claim?: Claim; events: EventOpt[]; prefill?: string }) {
    const editing = !!claim;
    const src = claim?.sources?.[0];

    const form = useForm({
        claim_text: claim?.claim_text ?? prefill ?? '',
        status: claim?.status ?? 'no_official_data',
        explanation: claim?.explanation ?? '',
        region: claim?.region ?? '',
        disaster_event_id: claim?.disaster_event_id ?? '',
        is_active: claim?.is_active ?? true,
        source_name: src?.name ?? '',
        source_url: src?.url ?? '',
        source_date: src?.published_at ?? '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editing) form.put(`/portal/claims/${claim!.id}`);
        else form.post('/portal/claims');
    };

    return (
        <PortalShell title={editing ? 'Edit Klaim' : 'Tambah Klaim Terverifikasi'}>
            <Head title="Klaim — Portal Relawan" />

            <form onSubmit={submit} className="max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-white p-6">
                <Area label="Teks klaim / kabar" required value={form.data.claim_text} onChange={(v) => form.setData('claim_text', v)} error={form.errors.claim_text} />
                <Select label="Status verifikasi" required value={form.data.status} onChange={(v) => form.setData('status', v)} options={STATUS} error={form.errors.status} />
                <Area label="Penjelasan (alasan + rujukan)" required rows={4} value={form.data.explanation} onChange={(v) => form.setData('explanation', v)} error={form.errors.explanation} />

                <div className="grid gap-5 sm:grid-cols-2">
                    <Text label="Wilayah (opsional)" value={form.data.region} onChange={(v) => form.setData('region', v)} error={form.errors.region} placeholder="mis. Binjai" />
                    <Select
                        label="Kaitkan ke peristiwa bencana (opsional)"
                        value={form.data.disaster_event_id}
                        onChange={(v) => form.setData('disaster_event_id', v)}
                        options={[{ value: '', label: '— tidak ada —' }, ...events.map((e) => ({ value: e.id, label: e.name }))]}
                    />
                </div>

                <div className="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p className="mb-3 text-sm font-medium text-slate-700">Sumber rujukan (wajib)</p>
                    <div className="space-y-4">
                        <Text label="Nama sumber" required value={form.data.source_name} onChange={(v) => form.setData('source_name', v)} error={form.errors.source_name} placeholder="mis. BMKG / MAFINDO" />
                        <div className="grid gap-4 sm:grid-cols-2">
                            <Text label="URL sumber" required type="url" value={form.data.source_url} onChange={(v) => form.setData('source_url', v)} error={form.errors.source_url} placeholder="https://…" />
                            <Text label="Tanggal sumber" type="date" value={form.data.source_date} onChange={(v) => form.setData('source_date', v)} error={form.errors.source_date} />
                        </div>
                    </div>
                </div>

                <label className="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" checked={form.data.is_active} onChange={(e) => form.setData('is_active', e.target.checked)} className="rounded border-slate-300" />
                    Aktif (dipakai dalam jawaban chat)
                </label>

                <div className="flex items-center gap-3 pt-2">
                    <button type="submit" disabled={form.processing} className="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                        {form.processing ? 'Menyimpan…' : editing ? 'Simpan perubahan' : 'Simpan & sinkronkan'}
                    </button>
                    <a href="/portal/claims" className="text-sm text-slate-500 hover:underline">
                        Batal
                    </a>
                </div>
            </form>
        </PortalShell>
    );
}
