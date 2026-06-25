<?php

namespace App\Services;

use App\Models\IntentLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Aggregated-insight layer over the chat intent logs ("Radar Tren").
 *
 * Two views:
 *  - Claim trends: claim_verification messages grouped by *similarity* (not exact
 *    text) into clusters, with a per-day series and a surge flag.
 *  - Region needs: shelter_location + aid_assistance messages grouped by the
 *    region named in the message, with a per-day series and a surge flag.
 *
 * IMPORTANT framing: results are signals from our own system's traffic, not a
 * statistical measurement of real-world hoax spread. The "surge" flag is a
 * "needs attention" hint for a human, never a confirmed fact. Callers decide
 * whether to include simulated demo rows (is_simulated = true).
 */
class RadarService
{
    /** Indonesian + filler stopwords stripped before similarity clustering. */
    private const STOPWORDS = [
        'yang', 'dan', 'di', 'ke', 'dari', 'apakah', 'apa', 'benar', 'kah', 'itu', 'ini',
        'ada', 'akan', 'sudah', 'tidak', 'untuk', 'dengan', 'pada', 'saya', 'kami', 'kah',
        'tentang', 'kabar', 'berita', 'info', 'informasi', 'mohon', 'tolong', 'gimana',
        'bagaimana', 'adakah', 'dimana', 'mana', 'kapan', 'sekarang', 'hari', 'katanya',
    ];

    /**
     * Claim-verification clusters within the window, most active first.
     *
     * @return array<int, array{label: string, total: int, current: int, previous: int, trend_pct: int|null, is_surging: bool, series: array<int, array{date: string, count: int}>}>
     */
    public function claimTrends(int $days = 7, string $source = 'all', int $maxClusters = 8): array
    {
        $logs = $this->logs(['claim_verification'], $days, $source);

        $clusters = $this->cluster($logs);

        return collect($clusters)
            ->map(fn (array $members) => $this->describeGroup(
                $this->representative($members),
                collect($members),
                $days,
            ))
            ->sortByDesc('total')
            ->take($maxClusters)
            ->values()
            ->all();
    }

    /**
     * Shelter/aid needs grouped by region within the window, most active first.
     *
     * @return array<int, array{label: string, total: int, current: int, previous: int, trend_pct: int|null, is_surging: bool, series: array<int, array{date: string, count: int}>}>
     */
    public function regionNeeds(int $days = 7, string $source = 'all'): array
    {
        $logs = $this->logs(['shelter_location', 'aid_assistance'], $days, $source)
            ->filter(fn (IntentLog $log) => filled($log->region));

        return $logs
            ->groupBy('region')
            ->map(fn (Collection $members, string $region) => $this->describeGroup($region, $members, $days))
            ->sortByDesc('total')
            ->values()
            ->all();
    }

    /**
     * Headline counts so the UI can honestly label how much is simulated.
     *
     * @return array{live: int, simulated: int, window_days: int}
     */
    public function meta(int $days, string $source): array
    {
        $base = IntentLog::where('created_at', '>=', now()->subDays($days));

        return [
            'live' => (clone $base)->where('is_simulated', false)->count(),
            'simulated' => (clone $base)->where('is_simulated', true)->count(),
            'window_days' => $days,
        ];
    }

    /**
     * @param  array<int, string>  $intents
     * @return Collection<int, IntentLog>
     */
    private function logs(array $intents, int $days, string $source): Collection
    {
        return IntentLog::query()
            ->whereIntentIn($intents)
            ->where('created_at', '>=', now()->subDays($days))
            ->when($source === 'live', fn ($q) => $q->where('is_simulated', false))
            ->when($source === 'simulated', fn ($q) => $q->where('is_simulated', true))
            ->orderBy('created_at')
            ->get(['id', 'user_message', 'region', 'detected_intent', 'is_simulated', 'created_at']);
    }

    /**
     * Greedy similarity clustering: each message joins the first existing cluster
     * whose seed message is similar enough (token Jaccard), else seeds a new one.
     * Captures "klaim sejenis", not byte-identical duplicates.
     *
     * @param  Collection<int, IntentLog>  $logs
     * @return array<int, array<int, IntentLog>>
     */
    private function cluster(Collection $logs, float $threshold = 0.34): array
    {
        /** @var array<int, array{seed: array<int, string>, members: array<int, IntentLog>}> $clusters */
        $clusters = [];

        foreach ($logs as $log) {
            $tokens = $this->tokens($log->user_message);

            if (empty($tokens)) {
                continue;
            }

            $matchedIndex = null;
            $bestScore = $threshold;

            foreach ($clusters as $i => $cluster) {
                $score = $this->jaccard($tokens, $cluster['seed']);
                if ($score >= $bestScore) {
                    $bestScore = $score;
                    $matchedIndex = $i;
                }
            }

            if ($matchedIndex === null) {
                $clusters[] = ['seed' => $tokens, 'members' => [$log]];
            } else {
                $clusters[$matchedIndex]['members'][] = $log;
            }
        }

        return array_map(fn (array $c) => $c['members'], $clusters);
    }

    /**
     * Build the per-day series + surge comparison shared by both views.
     *
     * @param  Collection<int, IntentLog>  $members
     * @return array{label: string, total: int, current: int, previous: int, trend_pct: int|null, is_surging: bool, series: array<int, array{date: string, count: int}>}
     */
    private function describeGroup(string $label, Collection $members, int $days): array
    {
        $series = $this->dailySeries($members, $days);

        $half = (int) ceil($days / 2);
        $previous = array_sum(array_map(fn ($p) => $p['count'], array_slice($series, 0, $days - $half)));
        $current = array_sum(array_map(fn ($p) => $p['count'], array_slice($series, $days - $half)));

        $trendPct = $previous > 0 ? (int) round(($current - $previous) / $previous * 100) : null;

        // A "needs attention" signal, deliberately conservative: recent half must
        // both out-pace the earlier half and clear a small floor.
        $isSurging = $current >= 3 && $current > $previous;

        return [
            'label' => $label,
            'total' => $members->count(),
            'current' => $current,
            'previous' => $previous,
            'trend_pct' => $trendPct,
            'is_surging' => $isSurging,
            'series' => $series,
        ];
    }

    /**
     * Daily counts across the window, oldest day first, zero-filled.
     *
     * @param  Collection<int, IntentLog>  $members
     * @return array<int, array{date: string, count: int}>
     */
    private function dailySeries(Collection $members, int $days): array
    {
        $counts = $members->groupBy(fn (IntentLog $log) => $log->created_at->toDateString())
            ->map->count();

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $series[] = ['date' => $date, 'count' => (int) ($counts[$date] ?? 0)];
        }

        return $series;
    }

    /**
     * The most representative message of a cluster: the shortest one tends to be
     * the clearest phrasing of the shared claim.
     *
     * @param  array<int, IntentLog>  $members
     */
    private function representative(array $members): string
    {
        return collect($members)
            ->sortBy(fn (IntentLog $log) => mb_strlen($log->user_message))
            ->first()
            ->user_message;
    }

    /**
     * @return array<int, string>
     */
    private function tokens(string $text): array
    {
        $normalized = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', mb_strtolower($text));
        $words = preg_split('/\s+/', trim((string) $normalized)) ?: [];

        return array_values(array_unique(array_filter(
            $words,
            fn (string $w) => mb_strlen($w) > 2 && ! in_array($w, self::STOPWORDS, true),
        )));
    }

    /**
     * @param  array<int, string>  $a
     * @param  array<int, string>  $b
     */
    private function jaccard(array $a, array $b): float
    {
        if (empty($a) || empty($b)) {
            return 0.0;
        }

        $intersection = count(array_intersect($a, $b));
        $union = count(array_unique([...$a, ...$b]));

        return $union > 0 ? $intersection / $union : 0.0;
    }
}
