import type { EscalationContact } from '@/hooks/useChat';

type Props = {
    contacts: EscalationContact[];
};

export default function EscalationPanel({ contacts }: Props) {
    if (!contacts.length) return null;

    return (
        <div className="mt-3 rounded-lg border border-red-100 bg-red-50 p-3">
            <p className="mb-2 text-xs font-semibold tracking-wide text-red-700 uppercase">
                Hubungi petugas resmi
            </p>
            <div className="space-y-1.5">
                {contacts.map((c, i) => (
                    <div
                        key={i}
                        className="flex items-baseline justify-between gap-3"
                    >
                        <span className="text-xs font-medium text-red-800">
                            {c.name}
                        </span>
                        <span className="shrink-0 font-mono text-xs text-red-600">
                            {c.contact}
                        </span>
                    </div>
                ))}
            </div>
        </div>
    );
}
