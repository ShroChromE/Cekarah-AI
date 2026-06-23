import { useEffect, useRef, useState } from 'react';

export type EscalationContact = {
    name: string;
    contact: string;
    available: string;
};

export type Source = {
    title: string;
    source_name: string;
    is_stale: boolean;
};

export type UserMessage = {
    role: 'user';
    content: string;
};

export type AssistantMessage = {
    role: 'assistant';
    reply: string;
    intent: string;
    confidence: number;
    escalation_suggested: boolean;
    escalation_contacts: EscalationContact[];
    sources_used: Source[];
    isStreaming?: boolean;
    status?: string;
};

export type ChatMessage = UserMessage | AssistantMessage;

const emptyAssistant = (): AssistantMessage => ({
    role: 'assistant',
    reply: '',
    intent: '',
    confidence: 0,
    escalation_suggested: false,
    escalation_contacts: [],
    sources_used: [],
    isStreaming: true,
    status: undefined,
});

export function useChat() {
    const [messages, setMessages] = useState<ChatMessage[]>([]);
    const [token, setToken] = useState<string | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const bottomRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        fetch('/api/chat-sessions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
        })
            .then((r) => r.json())
            .then((data) => setToken(data.token))
            .catch(() => setError('Gagal memulai sesi. Muat ulang halaman.'));
    }, []);

    useEffect(() => {
        bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages, isLoading]);

    /** Patch the trailing assistant message (the one currently streaming). */
    const patchAssistant = (patch: Partial<AssistantMessage>) => {
        setMessages((prev) => {
            const copy = [...prev];
            const last = copy[copy.length - 1];
            if (last && last.role === 'assistant') {
                copy[copy.length - 1] = { ...last, ...patch };
            }
            return copy;
        });
    };

    const appendReply = (text: string) => {
        setMessages((prev) => {
            const copy = [...prev];
            const last = copy[copy.length - 1];
            if (last && last.role === 'assistant') {
                copy[copy.length - 1] = {
                    ...last,
                    reply: last.reply + text,
                    status: undefined,
                };
            }
            return copy;
        });
    };

    const send = async (content: string) => {
        if (!content.trim() || isLoading || !token) return;

        setMessages((prev) => [
            ...prev,
            { role: 'user', content },
            emptyAssistant(),
        ]);
        setIsLoading(true);
        setError(null);

        try {
            const res = await fetch(
                `/api/chat-sessions/${token}/messages/stream`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'text/event-stream',
                    },
                    body: JSON.stringify({ content }),
                },
            );

            if (!res.body) throw new Error('No stream body');

            const reader = res.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            for (;;) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const frames = buffer.split('\n\n');
                buffer = frames.pop() ?? '';

                for (const frame of frames) {
                    const line = frame.trim();
                    if (!line.startsWith('data:')) continue;

                    let evt: Record<string, unknown>;
                    try {
                        evt = JSON.parse(line.slice(5).trim());
                    } catch {
                        continue;
                    }

                    if (evt.type === 'status') {
                        patchAssistant({ status: evt.message as string });
                    } else if (evt.type === 'chunk') {
                        appendReply(evt.content as string);
                    } else if (evt.type === 'done' || evt.type === 'error') {
                        patchAssistant({
                            reply: (evt.reply as string) ?? '',
                            intent: (evt.intent as string) ?? 'unclear',
                            confidence: (evt.confidence as number) ?? 0,
                            escalation_suggested:
                                (evt.escalation_suggested as boolean) ?? false,
                            escalation_contacts:
                                (evt.escalation_contacts as EscalationContact[]) ??
                                [],
                            sources_used: (evt.sources_used as Source[]) ?? [],
                            isStreaming: false,
                            status: undefined,
                        });
                    }
                }
            }

            // Safety net: if the stream ended without a terminal event.
            patchAssistant({ isStreaming: false, status: undefined });
        } catch {
            patchAssistant({
                reply: 'Koneksi terputus. Untuk darurat hubungi BNPB 117 ext 7 atau Basarnas 115.',
                intent: 'error',
                escalation_suggested: true,
                escalation_contacts: [
                    { name: 'BNPB', contact: '117 ext 7', available: '24 jam' },
                    { name: 'Basarnas', contact: '115', available: '24 jam' },
                ],
                isStreaming: false,
                status: undefined,
            });
        } finally {
            setIsLoading(false);
        }
    };

    return { messages, token, isLoading, error, send, bottomRef };
}
