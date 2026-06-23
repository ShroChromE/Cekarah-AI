import type { Source } from '@/hooks/useChat';

type Props = {
    source: Source;
};

export default function SourceCard({ source }: Props) {
    return (
        <span
            className={`inline-flex items-center gap-1.5 rounded border px-2 py-1 text-xs transition-colors ${
                source.is_stale
                    ? 'border-amber-200 bg-amber-50 text-amber-700'
                    : 'border-slate-200 bg-slate-50 text-slate-500'
            }`}
            title={source.title}
        >
            {source.is_stale && <span aria-hidden>⚠</span>}
            <span>{source.source_name}</span>
            {source.is_stale && (
                <span className="text-amber-500">
                    · mungkin sudah diperbarui
                </span>
            )}
        </span>
    );
}
