import BrandMark from '@/components/BrandMark';
import ConfidenceBar from '@/components/ConfidenceBar';
import EscalationPanel from '@/components/EscalationPanel';
import ReferenceList from '@/components/ReferenceList';
import ShelterMap from '@/components/ShelterMap';
import SourceCard from '@/components/SourceCard';
import type { ChatMessage } from '@/hooks/useChat';

const INTENT_LABELS: Record<string, string> = {
    disaster_info: 'Informasi bencana',
    claim_verification: 'Verifikasi klaim',
    shelter_location: 'Lokasi posko',
    aid_assistance: 'Bantuan sosial',
    out_of_scope: 'Di luar topik',
};

const INTENT_DOTS: Record<string, string> = {
    disaster_info: 'bg-blue-500',
    claim_verification: 'bg-violet-500',
    shelter_location: 'bg-emerald-500',
    aid_assistance: 'bg-amber-500',
    out_of_scope: 'bg-slate-400',
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
        references,
        locations,
        isStreaming,
        status,
    } = msg;
    const isError = intent === 'error';
    const showEscalation =
        (escalation_suggested ||
            (typeof confidence === 'number' && confidence < 0.6)) &&
        escalation_contacts?.length > 0;
    const hasReferences = !!references && references.length > 0;
    const hasMeta =
        intent ||
        typeof confidence === 'number' ||
        hasReferences ||
        sources_used?.length > 0 ||
        showEscalation;

    // Still thinking: a tool is running and no reply text has arrived yet.
    if (isStreaming && reply.length === 0) {
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
                            {status ?? 'Menyiapkan jawaban…'}
                        </span>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="animate-message-in flex justify-start gap-3">
            <BrandMark size={32} className="mt-0.5 shrink-0" />

            <div className="min-w-0 flex-1">
                <p className="mb-1 text-xs font-semibold text-slate-500">
                    Cekarah
                </p>

                <div className="text-sm leading-relaxed whitespace-pre-wrap text-slate-800">
                    {reply}
                    {isStreaming && (
                        <span className="animate-blink ml-0.5 inline-block h-4 w-0.5 translate-y-0.5 bg-slate-400" />
                    )}
                </div>

                {!isStreaming && locations && locations.length > 0 && (
                    <ShelterMap locations={locations} />
                )}

                {!isStreaming && !isError && hasMeta && (
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

                        {/* Prefer authoritative references (with clickable URLs); fall
                            back to the model's source list when none were captured. */}
                        {hasReferences ? (
                            <ReferenceList references={references} />
                        ) : (
                            sources_used?.length > 0 && (
                                <div className="flex flex-wrap gap-1.5 pt-1">
                                    {sources_used.map((s, i) => (
                                        <SourceCard key={i} source={s} />
                                    ))}
                                </div>
                            )
                        )}

                        {showEscalation && (
                            <EscalationPanel contacts={escalation_contacts} />
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
