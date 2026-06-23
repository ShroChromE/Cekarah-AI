import MessageBubble from '@/components/MessageBubble';
import TypingIndicator from '@/components/TypingIndicator';
import { useChat } from '@/hooks/useChat';
import { Link } from '@inertiajs/react';
import { useRef, useState } from 'react';

const EXAMPLE_QUESTIONS = [
    'Rumah saya kena banjir, butuh bantuan darurat',
    'Benarkah ada peringatan tsunami di Aceh malam ini?',
    'Cara daftar bantuan sosial sebagai korban bencana',
    'Nomor darurat bencana yang bisa dihubungi',
];

const MAX_CHARS = 2000;

export default function Chat() {
    const { messages, isLoading, error, send, bottomRef } = useChat();
    const [input, setInput] = useState('');
    const textareaRef = useRef<HTMLTextAreaElement>(null);

    const charsLeft = MAX_CHARS - input.length;
    const isOverLimit = charsLeft < 0;

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
        e.target.style.height = `${Math.min(e.target.scrollHeight, 128)}px`;
    };

    const handleKey = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    };

    return (
        <div className="flex h-screen flex-col bg-slate-50">
            {/* Header */}
            <header className="sticky top-0 z-10 border-b border-slate-800 bg-slate-900/95 backdrop-blur-md">
                <div className="mx-auto flex h-14 max-w-2xl items-center justify-between px-4">
                    <div className="flex items-center gap-3">
                        <div
                            className="h-6 w-0.5"
                            style={{ backgroundColor: '#E63946' }}
                        />
                        <span className="font-bold tracking-tight text-white">
                            Cekarah
                        </span>
                    </div>
                    <div className="flex items-center gap-4">
                        <Link
                            href="/about"
                            className="text-xs text-slate-500 transition-colors hover:text-slate-300"
                        >
                            Cara kerja
                        </Link>
                        <div className="flex items-center gap-1.5">
                            <span className="h-1.5 w-1.5 rounded-full bg-emerald-400" />
                            <span className="text-xs text-slate-500">
                                Sistem aktif
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            {/* Chat area */}
            <main className="flex-1 overflow-y-auto">
                <div className="mx-auto max-w-2xl px-4">
                    {messages.length === 0 && !isLoading ? (
                        <div className="pt-16 pb-8">
                            <p className="mb-2 text-2xl leading-snug font-bold text-slate-800">
                                Ceritakan situasimu.
                            </p>
                            <p className="mb-10 text-sm text-slate-500">
                                Atau mulai dari salah satu pertanyaan di bawah.
                            </p>

                            <div className="divide-y divide-slate-100">
                                {EXAMPLE_QUESTIONS.map((q) => (
                                    <button
                                        key={q}
                                        onClick={() => send(q)}
                                        className="group flex w-full items-center justify-between px-0 py-3.5 text-left transition-all duration-200 hover:pl-2"
                                    >
                                        <span className="text-sm text-slate-700 transition-colors group-hover:text-slate-900">
                                            {q}
                                        </span>
                                        <span className="text-sm text-slate-300 transition-all group-hover:translate-x-1 group-hover:text-slate-600">
                                            →
                                        </span>
                                    </button>
                                ))}
                            </div>
                        </div>
                    ) : (
                        <div className="space-y-6 py-6">
                            {messages.map((msg, i) => (
                                <MessageBubble key={i} {...msg} />
                            ))}

                            {isLoading && <TypingIndicator />}

                            {error && (
                                <p className="py-2 text-center text-sm text-red-600">
                                    {error}
                                </p>
                            )}

                            <div ref={bottomRef} />
                        </div>
                    )}
                </div>
            </main>

            {/* Input */}
            <div className="sticky bottom-0 border-t border-slate-100 bg-white px-4 py-3">
                <div className="mx-auto max-w-2xl">
                    <div
                        className={`flex items-end gap-2 rounded-xl border px-4 py-3 transition-all focus-within:bg-white ${
                            isOverLimit
                                ? 'border-red-400 bg-white'
                                : 'border-slate-200 bg-slate-50 focus-within:border-blue-400'
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
                            className="max-h-32 flex-1 resize-none overflow-y-auto bg-transparent text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none disabled:opacity-50"
                        />
                        <button
                            onClick={handleSend}
                            disabled={isLoading || !input.trim() || isOverLimit}
                            aria-label="Kirim pesan"
                            className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-30"
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
                                Enter untuk kirim · Shift+Enter untuk baris baru
                            </>
                        )}
                    </p>
                </div>
            </div>
        </div>
    );
}
