import { useEffect, useRef, useState } from 'react';

type Props = {
    /** Final integer value to count up to. */
    value: number;
    /** Animation duration in ms. */
    duration?: number;
    className?: string;
};

const easeOut = (t: number) => 1 - Math.pow(1 - t, 3);

/**
 * Counts from 0 to `value` once it scrolls into view, formatted with the
 * Indonesian thousands separator (e.g. 114200 -> "114.200").
 */
export default function CountUp({
    value,
    duration = 1600,
    className = '',
}: Props) {
    const ref = useRef<HTMLSpanElement | null>(null);
    const [display, setDisplay] = useState(0);

    useEffect(() => {
        const node = ref.current;
        if (!node) return;

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            setDisplay(value);
            return;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (!entry.isIntersecting) return;
                observer.disconnect();

                let raf = 0;
                const start = performance.now();
                const tick = (now: number) => {
                    const progress = Math.min((now - start) / duration, 1);
                    setDisplay(Math.round(easeOut(progress) * value));
                    if (progress < 1) raf = requestAnimationFrame(tick);
                };
                raf = requestAnimationFrame(tick);
                return () => cancelAnimationFrame(raf);
            },
            { threshold: 0.4 },
        );

        observer.observe(node);
        return () => observer.disconnect();
    }, [value, duration]);

    return (
        <span ref={ref} className={`tabular-nums ${className}`}>
            {display.toLocaleString('id-ID')}
        </span>
    );
}
