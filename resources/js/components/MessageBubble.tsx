import ConfidenceBar from '@/components/ConfidenceBar';
import EscalationPanel from '@/components/EscalationPanel';
import SourceCard from '@/components/SourceCard';
import type { ChatMessage } from '@/hooks/useChat';

const INTENT_LABELS: Record<string, string> = {
    navigasi: 'Navigasi bantuan',
    verifikasi: 'Verifikasi informasi',
    unclear: 'Klarifikasi dibutuhkan',
    error: 'Sistem error',
};

export default function MessageBubble(msg: ChatMessage) {
    if (msg.role === 'user') {
        return (
            <div className="flex justify-end">
                <div
                    className="max-w-[75%] rounded-[16px_16px_4px_16px] px-4 py-3 text-sm text-white"
                    style={{ backgroundColor: '#3B82F6' }}
                >
                    {msg.content}
                </div>
            </div>
        );
    }

    const { reply, intent, confidence, escalation_suggested, escalation_contacts, sources_used } = msg;

    return (
        <div className="flex justify-start">
            <div
                className="max-w-[85%] rounded-[4px_16px_16px_16px] border-l-[3px] bg-white px-4 py-3 shadow-sm"
                style={{ borderLeftColor: '#3B82F6' }}
            >
                <p className="text-sm leading-relaxed text-slate-800 whitespace-pre-wrap">{reply}</p>

                {intent && intent !== 'error' && (
                    <p className="mt-2 flex items-center gap-1.5 text-xs text-slate-400">
                        <span
                            className="inline-block h-1.5 w-1.5 rounded-full"
                            style={{
                                backgroundColor:
                                    intent === 'navigasi'
                                        ? '#3B82F6'
                                        : intent === 'verifikasi'
                                          ? '#8B5CF6'
                                          : '#94A3B8',
                            }}
                        />
                        {INTENT_LABELS[intent] ?? intent}
                    </p>
                )}

                {typeof confidence === 'number' && intent !== 'error' && (
                    <ConfidenceBar confidence={confidence} />
                )}

                {sources_used && sources_used.length > 0 && (
                    <div className="mt-3 space-y-1">
                        {sources_used.map((s, i) => (
                            <SourceCard key={i} source={s} />
                        ))}
                    </div>
                )}

                {(escalation_suggested || (typeof confidence === 'number' && confidence < 0.6)) &&
                    escalation_contacts &&
                    escalation_contacts.length > 0 && (
                        <EscalationPanel contacts={escalation_contacts} />
                    )}
            </div>
        </div>
    );
}
