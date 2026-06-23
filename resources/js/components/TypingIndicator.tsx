import BrandMark from '@/components/BrandMark';

export default function TypingIndicator() {
    return (
        <div className="animate-message-in flex justify-start gap-3">
            <BrandMark size={32} className="mt-0.5 shrink-0" />
            <div className="min-w-0 flex-1">
                <p className="mb-1 text-xs font-semibold text-slate-500">
                    Cekarah
                </p>
                <div className="flex items-center gap-2 py-1">
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
        </div>
    );
}
