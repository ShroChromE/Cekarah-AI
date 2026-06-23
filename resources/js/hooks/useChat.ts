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
};

export type ChatMessage = UserMessage | AssistantMessage;

export function useChat() {
    const [messages, setMessages] = useState<ChatMessage[]>([]);
    const [token, setToken] = useState<string | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const bottomRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        fetch('/api/chat-sessions', { method: 'POST', headers: { 'Content-Type': 'application/json' } })
            .then((r) => r.json())
            .then((data) => setToken(data.token))
            .catch(() => setError('Gagal memulai sesi. Muat ulang halaman.'));
    }, []);

    useEffect(() => {
        bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages, isLoading]);

    const send = async (content: string) => {
        if (!content.trim() || isLoading || !token) return;

        setMessages((prev) => [...prev, { role: 'user', content }]);
        setIsLoading(true);
        setError(null);

        try {
            const res = await fetch(`/api/chat-sessions/${token}/messages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ content }),
            });

            const data = await res.json();

            setMessages((prev) => [
                ...prev,
                {
                    role: 'assistant',
                    reply: data.reply,
                    intent: data.intent,
                    confidence: data.confidence,
                    escalation_suggested: data.escalation_suggested,
                    escalation_contacts: data.escalation_contacts ?? [],
                    sources_used: data.sources_used ?? [],
                },
            ]);
        } catch {
            setError('Koneksi terputus. Untuk darurat hubungi BNPB 117 ext 7.');
        } finally {
            setIsLoading(false);
        }
    };

    return { messages, token, isLoading, error, send, bottomRef };
}
