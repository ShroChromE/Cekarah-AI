import BrandMark from '@/components/BrandMark';
import { Form, Head, Link } from '@inertiajs/react';

export default function Login({ status }: { status?: string }) {
    return (
        <div className="flex min-h-screen items-center justify-center bg-slate-50 px-4">
            <Head title="Masuk — Portal Relawan" />
            <div className="w-full max-w-sm">
                <div className="mb-8 text-center">
                    <BrandMark size={40} className="mx-auto" />
                    <h1 className="mt-4 text-xl font-bold text-slate-900">
                        Portal Relawan Cekarah
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Masuk untuk mengelola data bantuan & verifikasi.
                    </p>
                </div>

                {status && (
                    <div className="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                        {status}
                    </div>
                )}

                <Form
                    action="/login"
                    method="post"
                    className="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    {({ errors, processing }) => (
                        <>
                            <div>
                                <label className="mb-1 block text-sm font-medium text-slate-700">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:outline-none"
                                />
                                {errors.email && (
                                    <p className="mt-1 text-xs text-red-600">
                                        {errors.email}
                                    </p>
                                )}
                            </div>

                            <div>
                                <label className="mb-1 block text-sm font-medium text-slate-700">
                                    Kata sandi
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    required
                                    className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:outline-none"
                                />
                                {errors.password && (
                                    <p className="mt-1 text-xs text-red-600">
                                        {errors.password}
                                    </p>
                                )}
                            </div>

                            <label className="flex items-center gap-2 text-sm text-slate-600">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    className="rounded border-slate-300"
                                />
                                Ingat saya
                            </label>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full rounded-lg bg-blue-600 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700 disabled:opacity-50"
                            >
                                {processing ? 'Memproses…' : 'Masuk'}
                            </button>
                        </>
                    )}
                </Form>

                <p className="mt-4 text-center text-sm text-slate-500">
                    Belum punya akun relawan?{' '}
                    <Link
                        href="/register"
                        className="font-medium text-blue-600 hover:underline"
                    >
                        Daftar
                    </Link>
                </p>
                <p className="mt-2 text-center text-xs text-slate-400">
                    <Link href="/" className="hover:underline">
                        ← Kembali ke chat publik
                    </Link>
                </p>
            </div>
        </div>
    );
}
