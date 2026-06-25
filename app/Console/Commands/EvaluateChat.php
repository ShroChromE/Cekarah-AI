<?php

namespace App\Console\Commands;

use App\Ai\Agents\CekarahAgent;
use App\Ai\Support\AgentReplyParser;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Laravel\Ai\Exceptions\FailoverableException;
use Throwable;

class EvaluateChat extends Command
{
    protected $signature = 'cekarah:evaluate
        {--limit=0 : Max cases per category (0 = all)}
        {--category= : Run only this intent category}';

    protected $description = 'Run the automated chat evaluation against the ground-truth set and report real metrics.';

    private const CATEGORIES = ['disaster_info', 'claim_verification', 'shelter_location', 'aid_assistance', 'out_of_scope'];

    public function handle(): int
    {
        $cases = $this->loadCases();

        if ($cases->isEmpty()) {
            $this->error('No evaluation cases matched the filters.');

            return self::FAILURE;
        }

        $this->info("Running {$cases->count()} evaluation cases (calls the agent directly — no DB writes)…");
        $this->newLine();

        $results = [];
        $bar = $this->output->createProgressBar($cases->count());
        $bar->start();

        foreach ($cases as $case) {
            $results[] = $this->evaluateCase($case);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $summary = $this->summarize($results);
        $this->renderSummary($summary);
        $reportPath = $this->writeReport($results, $summary);

        $this->newLine();
        $this->info("Report written to: {$reportPath}");

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function loadCases(): Collection
    {
        $all = collect(require database_path('data/evaluation_cases.php'));

        $category = $this->option('category');
        if ($category) {
            $all = $all->where('intent', $category);
        }

        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $all = $all->groupBy('intent')->map->take($limit)->flatten(1);
        }

        return $all->values();
    }

    /**
     * @param  array<string, mixed>  $case
     * @return array<string, mixed>
     */
    private function evaluateCase(array $case): array
    {
        $expectedIntent = $case['intent'];
        $start = microtime(true);
        $error = null;
        $data = [];

        try {
            $data = $this->promptWithRetry($case['q']);
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        $latencyMs = (int) round((microtime(true) - $start) * 1000);
        $reply = $data['reply'] ?? '';
        $detectedIntent = $error ? 'error' : ($data['intent'] ?? 'unclear');

        $hasCitation = $error ? false : $this->hasCitation($data);
        $detectedStatus = isset($case['status']) && ! $error ? $this->detectStatus($reply) : null;

        return [
            'question' => $case['q'],
            'expected_intent' => $expectedIntent,
            'detected_intent' => $detectedIntent,
            'intent_correct' => $detectedIntent === $expectedIntent,
            'expects_citation' => $expectedIntent !== 'out_of_scope',
            'has_citation' => $hasCitation,
            'expected_status' => $case['status'] ?? null,
            'detected_status' => $detectedStatus,
            'status_correct' => isset($case['status']) ? $detectedStatus === $case['status'] : null,
            'latency_ms' => $latencyMs,
            'error' => $error,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function promptWithRetry(string $question): array
    {
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $response = (new CekarahAgent)->prompt($question);

                return AgentReplyParser::parse($response->text);
            } catch (Throwable $e) {
                if (! $e instanceof FailoverableException || $attempt >= 2) {
                    throw $e;
                }

                usleep(2_000_000);
            }
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function hasCitation(array $data): bool
    {
        if (! empty($data['sources_used'])) {
            return true;
        }

        $reply = $data['reply'] ?? '';

        return (bool) preg_match('/https?:\/\/|\bsumber\b|BMKG|BNPB|BPBD|Kemensos|Basarnas|MAFINDO|PMI|Komdigi|Kemkomdigi|Kemendagri|Kemenko\s?PMK|Kemenkopmk/i', $reply);
    }

    /**
     * Best-effort detection of the verification verdict from the reply text.
     */
    private function detectStatus(string $reply): string
    {
        $r = mb_strtolower($reply);

        // Strong hoax indicators win first (a debunked claim often also notes the
        // absence of an official warning, which would otherwise look like
        // no_official_data). Weaker cues are checked afterwards.
        return match (true) {
            str_contains($r, 'hoaks') || str_contains($r, 'hoax') || str_contains($r, 'palsu')
                || str_contains($r, 'disinformasi') || str_contains($r, 'kabar bohong') => 'hoax',
            str_contains($r, 'belum ada data') || str_contains($r, 'tidak ada data resmi')
                || str_contains($r, 'belum terverifikasi') || str_contains($r, 'belum dapat dipastikan')
                || str_contains($r, 'tidak ada informasi resmi') || str_contains($r, 'belum ada informasi resmi')
                || str_contains($r, 'belum ada peringatan') || str_contains($r, 'tidak ada peringatan')
                || str_contains($r, 'belum ada konfirmasi') || str_contains($r, 'belum dikonfirmasi')
                || str_contains($r, 'tidak dapat dikonfirmasi') => 'no_official_data',
            str_contains($r, 'tidak benar') || str_contains($r, 'keliru') => 'hoax',
            str_contains($r, 'valid') || str_contains($r, 'terkonfirmasi') || str_contains($r, 'memang benar') => 'verified',
            default => 'unverified',
        };
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     * @return array<string, mixed>
     */
    private function summarize(array $results): array
    {
        $byCategory = [];

        foreach (self::CATEGORIES as $cat) {
            $rows = array_values(array_filter($results, fn ($r) => $r['expected_intent'] === $cat));
            if (empty($rows)) {
                continue;
            }

            $count = count($rows);
            $intentOk = count(array_filter($rows, fn ($r) => $r['intent_correct']));
            $citationRows = array_filter($rows, fn ($r) => $r['expects_citation']);
            $citationOk = count(array_filter($citationRows, fn ($r) => $r['has_citation']));
            $statusRows = array_filter($rows, fn ($r) => $r['status_correct'] !== null);
            $statusOk = count(array_filter($statusRows, fn ($r) => $r['status_correct']));
            $errors = count(array_filter($rows, fn ($r) => $r['error'] !== null));
            $latency = (int) round(array_sum(array_column($rows, 'latency_ms')) / $count);

            $byCategory[$cat] = [
                'count' => $count,
                'intent_acc' => $this->pct($intentOk, $count),
                'citation_pct' => $citationRows ? $this->pct($citationOk, count($citationRows)) : null,
                'status_acc' => $statusRows ? $this->pct($statusOk, count($statusRows)) : null,
                'avg_latency_ms' => $latency,
                'errors' => $errors,
            ];
        }

        $total = count($results);
        $oos = array_filter($results, fn ($r) => $r['expected_intent'] === 'out_of_scope');
        $citationRows = array_filter($results, fn ($r) => $r['expects_citation']);

        $overall = [
            'count' => $total,
            'intent_acc' => $this->pct(count(array_filter($results, fn ($r) => $r['intent_correct'])), $total),
            'citation_pct' => $citationRows ? $this->pct(count(array_filter($citationRows, fn ($r) => $r['has_citation'])), count($citationRows)) : null,
            'oos_rejection' => $oos ? $this->pct(count(array_filter($oos, fn ($r) => $r['detected_intent'] === 'out_of_scope')), count($oos)) : null,
            'avg_latency_ms' => $total ? (int) round(array_sum(array_column($results, 'latency_ms')) / $total) : 0,
            'errors' => count(array_filter($results, fn ($r) => $r['error'] !== null)),
        ];

        return ['by_category' => $byCategory, 'overall' => $overall];
    }

    private function pct(int $n, int $d): float
    {
        return $d > 0 ? round($n / $d * 100, 1) : 0.0;
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function renderSummary(array $summary): void
    {
        $rows = [];
        foreach ($summary['by_category'] as $cat => $m) {
            $rows[] = [
                $cat,
                $m['count'],
                $m['intent_acc'].'%',
                $m['citation_pct'] === null ? '—' : $m['citation_pct'].'%',
                $m['status_acc'] === null ? '—' : $m['status_acc'].'%',
                $m['avg_latency_ms'].' ms',
                $m['errors'],
            ];
        }

        $this->table(
            ['Kategori', 'N', 'Intent', 'Sitasi', 'Status klaim', 'Latency', 'Error'],
            $rows,
        );

        $o = $summary['overall'];
        $this->line('  <fg=cyan>Total intent accuracy   :</> '.$o['intent_acc'].'%');
        $this->line('  <fg=cyan>Total citation coverage :</> '.($o['citation_pct'] ?? '—').'%');
        $this->line('  <fg=cyan>Out-of-scope rejection  :</> '.($o['oos_rejection'] ?? '—').'%');
        $this->line('  <fg=cyan>Avg latency             :</> '.$o['avg_latency_ms'].' ms');
        $this->line('  <fg=cyan>Errors (overload/timeout):</> '.$o['errors'].' / '.$o['count']);
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     * @param  array<string, mixed>  $summary
     */
    private function writeReport(array $results, array $summary): string
    {
        $dir = storage_path('app/evaluations');
        File::ensureDirectoryExists($dir);
        $stamp = Carbon::now()->format('Y-m-d_His');

        File::put("{$dir}/eval-{$stamp}.json", json_encode([
            'generated_at' => Carbon::now()->toIso8601String(),
            'summary' => $summary,
            'results' => $results,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $md = $this->buildMarkdown($summary, $results, $stamp);
        $path = "{$dir}/eval-{$stamp}.md";
        File::put($path, $md);

        return $path;
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  array<int, array<string, mixed>>  $results
     */
    private function buildMarkdown(array $summary, array $results, string $stamp): string
    {
        $o = $summary['overall'];
        $md = "# Laporan Evaluasi Cekarah — {$stamp}\n\n";
        $md .= "Total kasus: {$o['count']} · dijalankan dengan memanggil agent langsung (tanpa menulis DB).\n\n";
        $md .= "## Ringkasan Keseluruhan\n\n";
        $md .= "| Metrik | Nilai |\n|---|---|\n";
        $md .= "| Akurasi klasifikasi intent | {$o['intent_acc']}% |\n";
        $md .= '| Jawaban menyertakan sumber | '.($o['citation_pct'] ?? '—')."% |\n";
        $md .= '| Keberhasilan menolak out-of-scope | '.($o['oos_rejection'] ?? '—')."% |\n";
        $md .= "| Latency rata-rata | {$o['avg_latency_ms']} ms |\n";
        $md .= "| Error (overload/timeout) | {$o['errors']} / {$o['count']} |\n\n";

        $md .= "## Per Kategori\n\n";
        $md .= "| Kategori | N | Intent | Sitasi | Status klaim | Latency | Error |\n|---|---|---|---|---|---|---|\n";
        foreach ($summary['by_category'] as $cat => $m) {
            $md .= "| {$cat} | {$m['count']} | {$m['intent_acc']}% | "
                .($m['citation_pct'] ?? '—').'% | '
                .($m['status_acc'] ?? '—').'% | '
                ."{$m['avg_latency_ms']} ms | {$m['errors']} |\n";
        }

        $failures = array_filter($results, fn ($r) => ! $r['intent_correct'] || ($r['status_correct'] === false) || $r['error']);
        if ($failures) {
            $md .= "\n## Kasus yang Perlu Diperhatikan\n\n";
            foreach ($failures as $f) {
                $reason = $f['error'] ? 'error: '.$f['error']
                    : (! $f['intent_correct'] ? "intent {$f['detected_intent']} (harusnya {$f['expected_intent']})"
                        : "status {$f['detected_status']} (harusnya {$f['expected_status']})");
                $md .= "- \"{$f['question']}\" → {$reason}\n";
            }
        }

        return $md;
    }
}
