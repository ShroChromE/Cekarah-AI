export default function TypingIndicator() {
    return (
        <div className="flex justify-start">
            <div
                className="rounded-[4px_16px_16px_16px] border-l-[3px] bg-white px-4 py-3 shadow-sm"
                style={{ borderLeftColor: '#3B82F6' }}
            >
                <p className="mb-2 text-xs text-slate-400">Mencari di knowledge base...</p>
                <div className="flex gap-1">
                    {[0, 150, 300].map((delay, i) => (
                        <span
                            key={i}
                            className="inline-block h-1.5 w-1.5 animate-bounce rounded-full bg-slate-400"
                            style={{ animationDelay: `${delay}ms` }}
                        />
                    ))}
                </div>
            </div>
        </div>
    );
}
