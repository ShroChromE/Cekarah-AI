import BrandMark from '@/components/BrandMark';
import { Form, Head, Link } from '@inertiajs/react';

export default function Register() {
    return (
        <div className="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-10">
            <Head title="Daftar — Portal Relawan" />
            <div className="w-full max-w-sm">
                <div className="mb-8 text-center">
                    <BrandMark size={40} className="mx-auto" />
                    <h1 className="mt-4 text-xl font-bold text-slate-900">
                        Daftar Relawan
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Buat akun untuk berkontribusi data resmi.
                    </p>
                </div>

                <Form
                    action="/register"
                    method="post"
                    className="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    {({ errors, processing }) => (
                        <>
                            <div>
                                <label className="mb-1 block text-sm font-medium text-slate-700">
                                    Nama
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    required
                                    autoFocus
                                    className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:outline-none"
                                />
                                {errors.name && (
                                    <p className="mt-1 text-xs text-red-600">
                                        {errors.name}
                                    </p>
                                )}
                            </div>

                            <div>
                                <label className="mb-1 block text-sm font-medium text-slate-700">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    required
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

                            <div>
                                <label className="mb-1 block text-sm font-medium text-slate-700">
                                    Ulangi kata sandi
                                </label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:outline-none"
                                />
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full rounded-lg bg-blue-600 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700 disabled:opacity-50"
                            >
                                {processing ? 'Memproses…' : 'Daftar'}
                            </button>
                        </>
                    )}
                </Form>

                <p className="mt-4 text-center text-sm text-slate-500">
                    Sudah punya akun?{' '}
                    <Link
                        href="/login"
                        className="font-medium text-blue-600 hover:underline"
                    >
                        Masuk
                    </Link>
                </p>
            </div>
        </div>
    );
}
