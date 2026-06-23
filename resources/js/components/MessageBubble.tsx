import BrandMark from '@/components/BrandMark';
import ConfidenceBar from '@/components/ConfidenceBar';
import EscalationPanel from '@/components/EscalationPanel';
import SourceCard from '@/components/SourceCard';
import type { ChatMessage } from '@/hooks/useChat';

const INTENT_LABELS: Record<string, string> = {
    navigasi: 'Navigasi bantuan',
    verifikasi: 'Verifikasi informasi',
    unclear: 'Butuh klarifikasi',
};

const INTENT_DOTS: Record<string, string> = {
    navigasi: 'bg-blue-500',
    verifikasi: 'bg-violet-500',
    unclear: 'bg-amber-500',
};

function UserGlyph() {
    return (
        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-slate-500">
            <svg
                className="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
            </svg>
        </div>
    );
}

export default function MessageBubble(msg: ChatMessage) {
    if (msg.role === 'user') {
        return (
            <div className="animate-message-in flex justify-end gap-3">
                <div className="max-w-[80%] rounded-2xl rounded-tr-md bg-slate-900 px-4 py-2.5 text-sm leading-relaxed text-white">
                    {msg.content}
                </div>
                <UserGlyph />
            </div>
        );
    }

    const {
        reply,
        intent,
        confidence,
        escalation_suggested,
        escalation_contacts,
        sources_used,
    } = msg;
    const isError = intent === 'error';
    const showEscalation =
        (escalation_suggested ||
            (typeof confidence === 'number' && confidence < 0.6)) &&
        escalation_contacts?.length > 0;

    return (
        <div className="animate-message-in flex justify-start gap-3">
            <BrandMark size={32} className="mt-0.5 shrink-0" />

            <div className="min-w-0 flex-1">
                <p className="mb-1 text-xs font-semibold text-slate-500">
                    Cekarah
                </p>

                <div className="text-sm leading-relaxed whitespace-pre-wrap text-slate-800">
                    {reply}
                </div>

                {!isError &&
                    (intent ||
                        typeof confidence === 'number' ||
                        sources_used?.length > 0 ||
                        showEscalation) && (
                        <div className="mt-3 space-y-2 border-t border-slate-100 pt-3">
                            {intent && INTENT_LABELS[intent] && (
                                <div className="flex items-center gap-2">
                                    <span
                                        className={`h-1.5 w-1.5 shrink-0 rounded-full ${INTENT_DOTS[intent] ?? 'bg-slate-400'}`}
                                    />
                                    <span className="text-xs text-slate-400">
                                        {INTENT_LABELS[intent]}
                                    </span>
                                </div>
                            )}

                            {typeof confidence === 'number' && (
                                <ConfidenceBar confidence={confidence} />
                            )}

                            {sources_used?.length > 0 && (
                                <div className="flex flex-wrap gap-1.5 pt-1">
                                    {sources_used.map((s, i) => (
                                        <SourceCard key={i} source={s} />
                                    ))}
                                </div>
                            )}

                            {showEscalation && (
                                <EscalationPanel
                                    contacts={escalation_contacts}
                                />
                            )}
                        </div>
                    )}
            </div>
        </div>
    );
}
