<?php

namespace App\Services;

/**
 * Lightweight, deterministic region extractor. It scans a chat message for a
 * known Indonesian place name (matching the regions seeded in the dataset and a
 * few common disaster-prone areas) and returns a canonical label.
 *
 * Deliberately keyword-based rather than LLM-based: the radar aggregates many
 * rows, so extraction must be cheap, offline, and predictable. A null result
 * simply means "no region named" — the row is excluded from the per-region view.
 */
class RegionExtractor
{
    /**
     * Canonical region => the lowercase aliases that map to it. Order matters
     * only for readability; matching checks every alias.
     *
     * @var array<string, array<int, string>>
     */
    private const REGIONS = [
        'Binjai' => ['binjai'],
        'Pidie Jaya' => ['pidie jaya', 'pidie', 'meureudu'],
        'Medan' => ['medan'],
        'Langkat' => ['langkat', 'stabat'],
        'Deli Serdang' => ['deli serdang', 'lubuk pakam'],
        'Aceh' => ['aceh', 'banda aceh'],
        'Sumatera Utara' => ['sumatera utara', 'sumut'],
        'Jakarta' => ['jakarta', 'dki'],
        'Bandung' => ['bandung'],
        'Garut' => ['garut'],
        'Cianjur' => ['cianjur'],
        'Sukabumi' => ['sukabumi'],
        'Semarang' => ['semarang'],
        'Demak' => ['demak'],
        'Surabaya' => ['surabaya'],
        'Palu' => ['palu'],
        'Lombok' => ['lombok'],
        'Padang' => ['padang'],
        'Cilegon' => ['cilegon'],
        'Serang' => ['serang'],
    ];

    /**
     * Return the canonical region named in the message, or null if none match.
     */
    public function extract(string $message): ?string
    {
        $haystack = ' '.mb_strtolower($message).' ';

        foreach (self::REGIONS as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                if (str_contains($haystack, $alias)) {
                    return $canonical;
                }
            }
        }

        return null;
    }
}
