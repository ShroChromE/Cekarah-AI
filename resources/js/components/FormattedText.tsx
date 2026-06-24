import type { ReactNode } from 'react';

// Inline markdown the model commonly emits: links, **bold**, *italic*.
// Links are matched first; bold before italic so "**x**" never matches as italic.
const PATTERN = /\[([^\]]+)\]\(([^)]+)\)|\*\*([^*]+)\*\*|\*([^*\n]+)\*/g;

/**
 * Renders a subset of inline markdown (bold, italic, links) as real elements.
 * Newlines are preserved by the parent's `whitespace-pre-wrap`. Unclosed marks
 * (e.g. mid-stream) simply render literally until their closer arrives.
 */
export default function FormattedText({ text }: { text: string }) {
    const nodes: ReactNode[] = [];
    let last = 0;
    let key = 0;
    let match: RegExpExecArray | null;

    PATTERN.lastIndex = 0;
    while ((match = PATTERN.exec(text)) !== null) {
        if (match.index > last) {
            nodes.push(text.slice(last, match.index));
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

    if (last < text.length) {
        nodes.push(text.slice(last));
    }

    return <>{nodes}</>;
}
