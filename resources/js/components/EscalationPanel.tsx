import type { EscalationContact } from '@/hooks/useChat';

type Props = {
    contacts: EscalationContact[];
};

export default function EscalationPanel({ contacts }: Props) {
    if (!contacts.length) return null;

    return (
        <div
            className="mt-3 rounded-md border px-4 py-3"
            style={{ backgroundColor: '#FEF2F2', borderColor: '#FECACA' }}
        >
            <p className="mb-2 text-xs font-semibold" style={{ color: '#B91C1C' }}>
                Hubungi petugas resmi untuk konfirmasi:
            </p>
            <ul className="space-y-1">
                {contacts.map((c, i) => (
                    <li key={i} className="text-xs" style={{ color: '#B91C1C' }}>
                        <span className="font-medium">{c.name}</span>
                        {' — '}
                        <span className="font-mono">{c.contact}</span>
                        <span className="ml-1 text-red-400">({c.available})</span>
                    </li>
                ))}
            </ul>
        </div>
    );
}
