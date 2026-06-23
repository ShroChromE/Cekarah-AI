import type { Source } from '@/hooks/useChat';

type Props = {
    source: Source;
};

export default function SourceCard({ source }: Props) {
    return (
        <div className="flex items-start gap-1.5 text-xs text-slate-500">
            <span className="mt-px text-slate-300">↳</span>
            <span>
                {source.title}
                <span className="ml-1 text-slate-400">· {source.source_name}</span>
                {source.is_stale && (
                    <span className="ml-1 text-amber-500" title="Data mungkin sudah diperbarui">
                        ⚠ Mungkin sudah diperbarui
                    </span>
                )}
            </span>
        </div>
    );
}
