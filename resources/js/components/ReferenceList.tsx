import type { Reference } from '@/hooks/useChat';

export default function ReferenceList({
    references,
}: {
    references: Reference[];
}) {
    if (!references.length) return null;

    return (
        <div>
            <p className="mb-1.5 text-xs font-medium text-slate-400">Sumber</p>
            <div className="flex flex-wrap gap-1.5">
                {references.map((ref, i) => {
                    const label = ref.date
                        ? `${ref.name} · ${ref.date}`
                        : ref.name;
                    const isLink = !!ref.url && /^https?:\/\//.test(ref.url);

                    return isLink ? (
                        <a
                            key={i}
                            href={ref.url ?? undefined}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1 rounded border border-slate-200 bg-slate-50 px-2 py-1 text-xs text-blue-600 transition-colors hover:border-blue-300 hover:bg-blue-50"
                        >
                            {label}
                            <span className="text-slate-400">↗</span>
                        </a>
                    ) : (
                        <span
                            key={i}
                            className="inline-flex items-center rounded border border-slate-200 bg-slate-50 px-2 py-1 text-xs text-slate-500"
                        >
                            {label}
                        </span>
                    );
                })}
            </div>
        </div>
    );
}
