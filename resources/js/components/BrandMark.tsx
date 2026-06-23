type Props = {
    /** Pixel size of the square mark. */
    size?: number;
    className?: string;
};

/**
 * The Cekarah mark: a radar-sweep motif on a deep-navy tile with a red signal
 * pulse — evoking "scanning & navigating" through a crisis. Used as the brand
 * logo and the assistant avatar.
 */
export default function BrandMark({ size = 32, className = '' }: Props) {
    return (
        <svg
            width={size}
            height={size}
            viewBox="0 0 32 32"
            fill="none"
            className={className}
            aria-hidden="true"
        >
            <rect width="32" height="32" rx="9" fill="#0A0F1E" />
            <circle
                cx="16"
                cy="16"
                r="9.5"
                stroke="#1E293B"
                strokeWidth="1.5"
            />
            <circle cx="16" cy="16" r="5" stroke="#334155" strokeWidth="1.5" />
            {/* Radar sweep */}
            <path
                d="M16 16 L16 5.5 A10.5 10.5 0 0 1 25 11 Z"
                fill="#E63946"
                fillOpacity="0.9"
            />
            <circle cx="16" cy="16" r="2.1" fill="#F8FAFC" />
        </svg>
    );
}
