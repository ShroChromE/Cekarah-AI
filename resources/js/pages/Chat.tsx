import MessageBubble from '@/components/MessageBubble';
import TypingIndicator from '@/components/TypingIndicator';
import { useChat } from '@/hooks/useChat';
import { Link } from '@inertiajs/react';
import { useState } from 'react';

const EXAMPLE_QUESTIONS = [
    'Rumah saya kena banjir, butuh bantuan darurat',
    'Benarkah ada peringatan tsunami di Aceh malam ini?',
    'Cara daftar bantuan sosial sebagai korban bencana',
    'Nomor darurat bencana yang bisa dihubungi',
];

export default function Chat() {
    const { messages, isLoading, error, send, bottomRef } = useChat();
    const [input, setInput] = useState('');

    const handleSend = () => {
        send(input);
        setInput('');
    };

    const handleKey = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    };

    return (
        <div className="flex h-screen flex-col bg-slate-50">
            <header className="flex items-center justify-between bg-slate-900 px-4 py-3 text-white">
                <div>
                    <h1 className="text-lg font-semibold tracking-tight">Cekarah</h1>
                    <p className="text-xs text-slate-400">Navigator bantuan & verifikasi informasi</p>
                </div>
                <div className="flex items-center gap-4">
                    <Link href="/about" className="text-xs text-slate-400 hover:text-slate-200 transition-colors">
                        Cara kerja
                    </Link>
                    <span className="rounded border border-slate-700 px-2 py-1 text-xs text-slate-500">
                        Navigator awal — bukan otoritas final
                    </span>
                </div>
            </header>

            <main className="mx-auto w-full max-w-2xl flex-1 overflow-y-auto px-4 py-6">
                {messages.length === 0 && !isLoading && (
                    <div className="pt-8 text-center">
                        <div className="mx-auto mb-8 max-w-xs">
                            <div
                                className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg"
                                style={{ backgroundColor: '#EFF6FF' }}
                            >
                                <span className="text-xl">🆘</span>
                            </div>
                            <p className="text-sm text-slate-500">
                                Tanyakan sesuatu atau pilih contoh di bawah
                            </p>
                        </div>
                        <div className="grid grid-cols-1 gap-2">
                            {EXAMPLE_QUESTIONS.map((q) => (
                                <button
                                    key={q}
                                    onClick={() => send(q)}
                                    className="rounded-lg border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-700 transition-colors hover:border-blue-400 hover:bg-blue-50"
                                >
                                    {q}
                                </button>
                            ))}
                        </div>
                    </div>
                )}

                <div className="space-y-4">
                    {messages.map((msg, i) => (
                        <MessageBubble key={i} {...msg} />
                    ))}

                    {isLoading && <TypingIndicator />}

                    {error && (
                        <p className="py-2 text-center text-sm text-red-600">{error}</p>
                    )}

                    <div ref={bottomRef} />
                </div>
            </main>

            <div className="border-t border-slate-200 bg-white px-4 py-3">
                <div className="mx-auto flex max-w-2xl gap-2">
                    <textarea
                        value={input}
                        onChange={(e) => setInput(e.target.value)}
                        onKeyDown={handleKey}
                        placeholder="Tulis pertanyaan atau ceritakan situasimu..."
                        rows={2}
                        disabled={isLoading}
                        className="flex-1 resize-none rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                    />
                    <button
                        onClick={handleSend}
                        disabled={isLoading || !input.trim()}
                        className="self-end rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Kirim
                    </button>
                </div>
                <p className="mx-auto mt-2 max-w-2xl text-xs text-slate-400">
                    Enter untuk kirim · Shift+Enter untuk baris baru ·{' '}
                    <Link href="/about" className="underline hover:text-slate-600">
                        Cara kerja sistem
                    </Link>
                </p>
            </div>
        </div>
    );
}
