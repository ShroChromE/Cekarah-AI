type Props = {
    confidence: number;
};

export default function ConfidenceBar({ confidence }: Props) {
    if (confidence === undefined || confidence === null) return null;

    const pct = Math.round((confidence ?? 0) * 100);
    const color = pct >= 70 ? '#3B82F6' : pct >= 50 ? '#F59E0B' : '#EF4444';
    const isLow = pct < 60;

    return (
        <div>
            <div
                className="w-full overflow-hidden rounded-full bg-slate-100"
                style={{ height: '2px' }}
            >
                <div
                    className="h-full rounded-full transition-all duration-700"
                    style={{ width: `${pct}%`, backgroundColor: color }}
                />
            </div>
            {isLow && (
                <p className="mt-1.5 flex items-center gap-1 text-xs text-amber-600">
                    <span aria-hidden>⚠</span>
                    <span>
                        Keyakinan rendah — verifikasi ke sumber resmi disarankan
                    </span>
                </p>
            )}
        </div>
    );
}
