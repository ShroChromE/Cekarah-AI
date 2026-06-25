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
        // Specific kabupaten/kota FIRST so multi-word names (e.g. "Aceh Tamiang",
        // "Padang Panjang") win before the generic province aliases below.
        'Pidie Jaya' => ['pidie jaya', 'pidie', 'meureudu'],
        'Aceh Tamiang' => ['aceh tamiang', 'kuala simpang', 'rantau'],
        'Aceh Besar' => ['aceh besar', 'jantho'],
        'Aceh Utara' => ['aceh utara', 'lhoksukon'],
        'Banda Aceh' => ['banda aceh'],
        'Bener Meriah' => ['bener meriah', 'redelong'],
        'Mandailing Natal' => ['mandailing natal', 'madina', 'panyabungan', 'batahan'],
        'Tapanuli Tengah' => ['tapanuli tengah', 'tapteng', 'pandan'],
        'Tapanuli Utara' => ['tapanuli utara', 'taput', 'tarutung'],
        'Tapanuli Selatan' => ['tapanuli selatan', 'tapsel'],
        'Deli Serdang' => ['deli serdang', 'lubuk pakam'],
        'Padang Panjang' => ['padang panjang'],
        'Padang Pariaman' => ['padang pariaman'],
        'Pesisir Selatan' => ['pesisir selatan', 'painan'],
        'Lima Puluh Kota' => ['lima puluh kota', 'limapuluh kota', 'sarilamak'],
        'Bukittinggi' => ['bukittinggi'],
        'Agam' => ['agam', 'lubuk basung', 'malalak'],
        'Binjai' => ['binjai'],
        'Langkat' => ['langkat', 'stabat'],
        'Medan' => ['medan'],
        'Padang' => ['kota padang', 'padang'],
        // Provinces / generic fallbacks LAST.
        'Aceh' => ['aceh'],
        'Sumatera Utara' => ['sumatera utara', 'sumut'],
        'Sumatera Barat' => ['sumatera barat', 'sumbar'],
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
