import { useEffect, useRef, useState, type ReactNode } from 'react';

type Props = {
    children: ReactNode;
    /** Delay before the reveal transition starts, in ms. */
    delay?: number;
    className?: string;
    as?: 'div' | 'section' | 'li';
};

/**
 * Reveals its children with a fade-and-rise once it scrolls into view.
 * Respects prefers-reduced-motion via the .reveal utility classes.
 */
export default function Reveal({
    children,
    delay = 0,
    className = '',
    as = 'div',
}: Props) {
    const ref = useRef<HTMLElement | null>(null);
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        const node = ref.current;
        if (!node) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setVisible(true);
                    observer.disconnect();
                }
            },
            { threshold: 0.15, rootMargin: '0px 0px -40px 0px' },
        );

        observer.observe(node);
        return () => observer.disconnect();
    }, []);

    const Tag = as;

    return (
        <Tag
            ref={ref as never}
            className={`reveal ${visible ? 'is-visible' : ''} ${className}`}
            style={{ transitionDelay: `${delay}ms` }}
        >
            {children}
        </Tag>
    );
}
