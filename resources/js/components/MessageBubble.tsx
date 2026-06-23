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

export default function MessageBubble(msg: ChatMessage) {
    if (msg.role === 'user') {
        return (
            <div className="flex justify-end">
                <div className="max-w-[80%] rounded-2xl rounded-tr-sm bg-slate-900 px-4 py-3 text-sm leading-relaxed text-white">
                    {msg.content}
                </div>
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
        <div className="flex justify-start">
            <div className="w-full max-w-[88%]">
                <div className="mb-3 text-sm leading-relaxed whitespace-pre-wrap text-slate-800">
                    {reply}
                </div>

                {!isError &&
                    (intent ||
                        typeof confidence === 'number' ||
                        sources_used?.length > 0 ||
                        showEscalation) && (
                        <div className="space-y-2 border-t border-slate-100 pt-3">
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
