export default function TypingIndicator() {
    return (
        <div className="flex justify-start">
            <div className="flex items-center gap-2 px-1 py-2">
                <div className="flex items-center gap-1">
                    {[0, 150, 300].map((delay) => (
                        <span
                            key={delay}
                            className="inline-block h-1.5 w-1.5 animate-bounce rounded-full bg-slate-300"
                            style={{
                                animationDelay: `${delay}ms`,
                                animationDuration: '1s',
                            }}
                        />
                    ))}
                </div>
                <span className="text-xs text-slate-400">
                    Mencari di knowledge base…
                </span>
            </div>
        </div>
    );
}
