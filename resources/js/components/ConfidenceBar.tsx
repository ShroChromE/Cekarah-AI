type Props = {
    confidence: number;
};

export default function ConfidenceBar({ confidence }: Props) {
    const pct = Math.round((confidence ?? 0) * 100);
    const color = pct >= 70 ? '#22C55E' : pct >= 50 ? '#F59E0B' : '#EF4444';

    return (
        <div className="mt-3">
            <div className="w-full rounded-full bg-slate-100" style={{ height: '3px' }}>
                <div
                    className="rounded-full transition-all duration-500"
                    style={{ width: `${pct}%`, height: '3px', backgroundColor: color }}
                />
            </div>
            {pct < 70 && (
                <p className="mt-1 text-xs text-amber-600">Verifikasi ke sumber resmi disarankan</p>
            )}
        </div>
    );
}
