import BrandMark from '@/components/BrandMark';
import { Link, router, usePage } from '@inertiajs/react';
import { useEffect, useState, type ReactNode } from 'react';

type PageProps = {
    auth: { user: { name: string; email: string; role: string } | null };
    flash: { status: string | null };
};

const NAV = [
    { label: 'Dashboard', href: '/portal', match: /^\/portal$/ },
    { label: 'Radar Tren', href: '/portal/radar', match: /^\/portal\/radar/ },
    { label: 'Perlu Ditinjau', href: '/portal/review', match: /^\/portal\/review/ },
    { label: 'Posko', href: '/portal/shelters', match: /^\/portal\/shelters/ },
    { label: 'Bantuan', href: '/portal/aid', match: /^\/portal\/aid/ },
    { label: 'Klaim', href: '/portal/claims', match: /^\/portal\/claims/ },
];

export default function PortalShell({ title, actions, children }: { title: string; actions?: ReactNode; children: ReactNode }) {
    const { props, url } = usePage<PageProps>();
    const user = props.auth?.user;
    const [flash, setFlash] = useState<string | null>(props.flash?.status ?? null);

    useEffect(() => {
        setFlash(props.flash?.status ?? null);
        if (props.flash?.status) {
            const t = setTimeout(() => setFlash(null), 4000);
            return () => clearTimeout(t);
        }
    }, [props.flash?.status]);

    return (
        <div className="min-h-screen bg-slate-50">
            <header className="border-b border-slate-800 bg-[#0A0F1E]">
                <div className="mx-auto flex h-14 max-w-6xl items-center justify-between px-4">
                    <Link href="/portal" className="flex items-center gap-2.5">
                        <BrandMark size={24} />
                        <span className="text-sm font-bold tracking-tight text-white">
                            Cekarah <span className="font-normal text-slate-400">· Portal Relawan</span>
                        </span>
                    </Link>
                    <div className="flex items-center gap-4">
                        {user && <span className="hidden text-xs text-slate-400 sm:inline">{user.name}</span>}
                        <button
                            onClick={() => router.post('/logout')}
                            className="text-xs text-slate-400 transition-colors hover:text-white"
                        >
                            Keluar
                        </button>
                    </div>
                </div>
                <nav className="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-3">
                    {NAV.map((item) => {
                        const active = item.match.test(url.split('?')[0]);
                        return (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={`whitespace-nowrap border-b-2 px-3 py-2.5 text-sm transition-colors ${
                                    active
                                        ? 'border-blue-400 text-white'
                                        : 'border-transparent text-slate-400 hover:text-slate-200'
                                }`}
                            >
                                {item.label}
                            </Link>
                        );
                    })}
                </nav>
            </header>

            {flash && (
                <div className="mx-auto mt-4 max-w-6xl px-4">
                    <div className="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-700">
                        {flash}
                    </div>
                </div>
            )}

            <main className="mx-auto max-w-6xl px-4 py-8">
                <div className="mb-6 flex items-center justify-between gap-4">
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">{title}</h1>
                    {actions}
                </div>
                {children}
            </main>
        </div>
    );
}
