import type { ReactNode } from 'react';

const base =
    'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100';

function Label({ children, required }: { children: ReactNode; required?: boolean }) {
    return (
        <label className="mb-1 block text-sm font-medium text-slate-700">
            {children}
            {required && <span className="text-red-500"> *</span>}
        </label>
    );
}

function Error({ message }: { message?: string }) {
    return message ? <p className="mt-1 text-xs text-red-600">{message}</p> : null;
}

export function Text({
    label,
    value,
    onChange,
    error,
    required,
    type = 'text',
    placeholder,
}: {
    label: string;
    value: string | number;
    onChange: (v: string) => void;
    error?: string;
    required?: boolean;
    type?: string;
    placeholder?: string;
}) {
    return (
        <div>
            <Label required={required}>{label}</Label>
            <input type={type} value={value} placeholder={placeholder} onChange={(e) => onChange(e.target.value)} className={base} />
            <Error message={error} />
        </div>
    );
}

export function Area({
    label,
    value,
    onChange,
    error,
    required,
    rows = 3,
}: {
    label: string;
    value: string;
    onChange: (v: string) => void;
    error?: string;
    required?: boolean;
    rows?: number;
}) {
    return (
        <div>
            <Label required={required}>{label}</Label>
            <textarea value={value} rows={rows} onChange={(e) => onChange(e.target.value)} className={`${base} resize-y`} />
            <Error message={error} />
        </div>
    );
}

export function Select({
    label,
    value,
    onChange,
    options,
    error,
    required,
}: {
    label: string;
    value: string | number;
    onChange: (v: string) => void;
    options: { value: string | number; label: string }[];
    error?: string;
    required?: boolean;
}) {
    return (
        <div>
            <Label required={required}>{label}</Label>
            <select value={value} onChange={(e) => onChange(e.target.value)} className={base}>
                {options.map((o) => (
                    <option key={o.value} value={o.value}>
                        {o.label}
                    </option>
                ))}
            </select>
            <Error message={error} />
        </div>
    );
}
