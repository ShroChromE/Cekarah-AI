<?php

namespace App\Ai\Support;

use App\Models\Source;
use Illuminate\Support\Collection;

class ToolReferences
{
    /**
     * Normalize polymorphic Source models into reference rows for tool output.
     *
     * @param  iterable<int, Source>  $sources
     * @return array<int, array{name: string, url: string|null, date: string|null, simulated: bool}>
     */
    public static function fromSources(iterable $sources): array
    {
        return (new Collection($sources))
            ->map(fn (Source $s): array => [
                'name' => $s->name,
                'url' => $s->url,
                'date' => $s->published_at?->toDateString(),
                'simulated' => (bool) $s->is_simulated,
            ])
            ->all();
    }

    /**
     * Normalize knowledge-base similarity chunks into reference rows.
     *
     * @param  array<int, array{source_name?: string, source_url?: string|null, is_stale?: bool}>  $chunks
     * @return array<int, array{name: string, url: string|null, date: string|null, is_stale: bool}>
     */
    public static function fromChunks(array $chunks): array
    {
        return (new Collection($chunks))
            ->map(fn (array $c): array => [
                'name' => $c['source_name'] ?? 'Knowledge base Cekarah',
                'url' => $c['source_url'] ?? null,
                'date' => null,
                'is_stale' => (bool) ($c['is_stale'] ?? false),
            ])
            ->unique('name')
            ->values()
            ->all();
    }
}
