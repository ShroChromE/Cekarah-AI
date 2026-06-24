import type { ReactNode } from 'react';

// Inline markdown the model commonly emits: links, **bold**, *italic*.
// Links first; bold before italic so "**x**" never matches as italic. Italic
// requires a non-space, non-asterisk right after the opening "*" so it can't
// latch onto list markers or stray asterisks.
const PATTERN = /\[([^\]]+)\]\(([^)]+)\)|\*\*([^*]+)\*\*|\*([^\s*][^*\n]*?)\*/g;

/**
 * Renders a subset of inline markdown (bold, italic, links) as real elements.
 * Newlines are preserved by the parent's `whitespace-pre-wrap`. Unclosed marks
 * (e.g. mid-stream) simply render literally until their closer arrives.
 */
export default function FormattedText({ text }: { text: string }) {
    // Turn markdown list markers ("* item" / "- item") at line start into a
    // bullet glyph so a leading "*" is never mistaken for emphasis.
    const normalized = text.replace(/^[ \t]*[*-][ \t]+/gm, '• ');

    const nodes: ReactNode[] = [];
    let last = 0;
    let key = 0;
    let match: RegExpExecArray | null;

    PATTERN.lastIndex = 0;
    while ((match = PATTERN.exec(normalized)) !== null) {
        if (match.index > last) {
            nodes.push(normalized.slice(last, match.index));
        }

        const [full, linkText, linkUrl, bold, italic] = match;

        if (linkText !== undefined && linkUrl !== undefined) {
            nodes.push(
                /^https?:\/\//.test(linkUrl) ? (
                    <a
                        key={key++}
                        href={linkUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-blue-600 underline hover:text-blue-700"
                    >
                        {linkText}
                    </a>
                ) : (
                    linkText
                ),
            );
        } else if (bold !== undefined) {
            nodes.push(
                <strong key={key++} className="font-semibold text-slate-900">
                    {bold}
                </strong>,
            );
        } else if (italic !== undefined) {
            nodes.push(<em key={key++}>{italic}</em>);
        }

        last = match.index + full.length;
    }

    if (last < normalized.length) {
        nodes.push(normalized.slice(last));
    }

    return <>{nodes}</>;
}
