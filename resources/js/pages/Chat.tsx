import BrandMark from '@/components/BrandMark';
import MessageBubble from '@/components/MessageBubble';
import { useChat } from '@/hooks/useChat';
import { Link } from '@inertiajs/react';
import { useRef, useState, type ReactNode } from 'react';

type Example = { icon: ReactNode; text: string };

const EXAMPLE_QUESTIONS: Example[] = [
    {
        icon: (
            <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={1.8}
                d="M3 13h2l2-7 4 14 3-9 2 4h4"
            />
        ),
        text: 'Rumah saya kena banjir, butuh bantuan darurat',
    },
    {
        icon: (
            <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={1.8}
                d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"
            />
        ),
        text: 'Benarkah ada peringatan tsunami di Aceh malam ini?',
    },
    {
        icon: (
            <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={1.8}
                d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM5 21a7 7 0 0 1 14 0"
            />
        ),
        text: 'Cara daftar bantuan sosial sebagai korban bencana',
    },
    {
        icon: (
            <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={1.8}
                d="M3 5a2 2 0 0 1 2-2h2l2 5-2 1a11 11 0 0 0 5 5l1-2 5 2v2a2 2 0 0 1-2 2A16 16 0 0 1 3 5z"
            />
        ),
        text: 'Nomor darurat bencana yang bisa dihubungi',
    },
];

const MAX_CHARS = 2000;

export default function Chat() {
    const { messages, isLoading, error, send, bottomRef } = useChat();
    const [input, setInput] = useState('');
    const textareaRef = useRef<HTMLTextAreaElement>(null);

    const charsLeft = MAX_CHARS - input.length;
    const isOverLimit = charsLeft < 0;
    const isEmpty = messages.length === 0 && !isLoading;

    const resetHeight = () => {
        if (textareaRef.current) textareaRef.current.style.height = 'auto';
    };

    const handleSend = () => {
        if (isOverLimit) return;
        send(input);
        setInput('');
        resetHeight();
    };

    const handleInput = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
        setInput(e.target.value);
        e.target.style.height = 'auto';
        e.target.style.height = `${Math.min(e.target.scrollHeight, 160)}px`;
    };

    const handleKey = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    };

    return (
        <div className="flex h-screen flex-col bg-[#FAFAF9]">
            {/* Header */}
            <header className="sticky top-0 z-10 border-b border-slate-800/60 bg-[#0A0F1E]">
                <div className="mx-auto flex h-14 max-w-3xl items-center justify-between px-4">
                    <Link href="/" className="group flex items-center gap-2.5">
                        <BrandMark size={26} />
                        <span className="font-bold tracking-tight text-white">
                            Cekarah
                        </span>
                    </Link>
                    <div className="flex items-center gap-5">
                        <Link
                            href="/about"
                            className="text-xs text-slate-400 transition-colors hover:text-white"
                        >
                            Cara kerja
                        </Link>
                        <div className="flex items-center gap-1.5">
                            <span className="relative flex h-1.5 w-1.5">
                                <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75" />
                                <span className="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-400" />
                            </span>
                            <span className="text-xs text-slate-400">
                                Sistem aktif
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            {/* Thread */}
            <main className="flex-1 overflow-y-auto">
                <div className="mx-auto max-w-3xl px-4">
                    {isEmpty ? (
                        <div className="flex min-h-[60vh] flex-col items-center justify-center py-10 text-center">
                            <div className="animate-message-in">
                                <BrandMark size={52} className="mx-auto" />
                            </div>
                            <h1 className="mt-6 text-2xl font-bold tracking-tight text-slate-900">
                                Ada yang bisa Cekarah bantu?
                            </h1>
                            <p className="mt-2 max-w-sm text-sm leading-relaxed text-slate-500">
                                Navigasi bantuan darurat atau verifikasi
                                informasi bencana. Ceritakan situasimu dengan
                                kata-katamu sendiri.
                            </p>

                            <div className="mt-9 grid w-full max-w-xl gap-2 sm:grid-cols-2">
                                {EXAMPLE_QUESTIONS.map((q) => (
                                    <button
                                        key={q.text}
                                        onClick={() => send(q.text)}
                                        className="group flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3.5 text-left transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-sm"
                                    >
                                        <span className="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-400 transition-colors group-hover:bg-blue-50 group-hover:text-blue-500">
                                            <svg
                                                className="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                {q.icon}
                                            </svg>
                                        </span>
                                        <span className="text-sm leading-snug text-slate-700 transition-colors group-hover:text-slate-900">
                                            {q.text}
                                        </span>
                                    </button>
                                ))}
                            </div>
                        </div>
                    ) : (
                        <div className="space-y-7 py-6">
                            {messages.map((msg, i) => (
                                <MessageBubble key={i} {...msg} />
                            ))}

                            {error && (
                                <div className="rounded-lg border border-red-100 bg-red-50 px-4 py-2.5 text-center text-sm text-red-600">
                                    {error}
                                </div>
                            )}

                            <div ref={bottomRef} />
                        </div>
                    )}
                </div>
            </main>

            {/* Composer */}
            <div className="sticky bottom-0 bg-linear-to-t from-[#FAFAF9] via-[#FAFAF9] to-transparent px-4 pt-4 pb-3">
                <div className="mx-auto max-w-3xl">
                    <div
                        className={`flex items-end gap-2 rounded-2xl border bg-white px-4 py-3 shadow-sm transition-all ${
                            isOverLimit
                                ? 'border-red-400'
                                : 'border-slate-200 focus-within:border-blue-400 focus-within:shadow-md'
                        }`}
                    >
                        <textarea
                            ref={textareaRef}
                            value={input}
                            onChange={handleInput}
                            onKeyDown={handleKey}
                            placeholder="Tulis pertanyaan atau ceritakan situasimu..."
                            rows={1}
                            disabled={isLoading}
                            className="max-h-40 flex-1 resize-none overflow-y-auto bg-transparent text-sm leading-relaxed text-slate-800 placeholder:text-slate-400 focus:outline-none disabled:opacity-50"
                        />
                        <button
                            onClick={handleSend}
                            disabled={isLoading || !input.trim() || isOverLimit}
                            aria-label="Kirim pesan"
                            className="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white transition-all hover:bg-blue-700 hover:shadow active:scale-95 disabled:cursor-not-allowed disabled:bg-slate-300"
                        >
                            <svg
                                className="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2.5}
                                    d="M12 19V5m0 0l-7 7m7-7l7 7"
                                />
                            </svg>
                        </button>
                    </div>
                    <p className="mt-2 text-center text-xs text-slate-400">
                        {isOverLimit ? (
                            <span className="text-red-500">
                                Terlalu panjang ({Math.abs(charsLeft)} karakter
                                lebih)
                            </span>
                        ) : charsLeft <= 200 ? (
                            <span>{charsLeft} karakter tersisa</span>
                        ) : (
                            <>
                                Cekarah bisa keliru — selalu verifikasi ke
                                sumber & petugas resmi.
                            </>
                        )}
                    </p>
                </div>
            </div>
        </div>
    );
}
