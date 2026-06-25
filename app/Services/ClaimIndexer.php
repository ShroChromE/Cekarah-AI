<?php

namespace App\Services;

use App\Models\ClaimVerification;
use Laravel\Ai\Embeddings;

/**
 * Keeps claim_verifications retrievable by the verify_claim tool: generates an
 * embedding for each claim and runs in-PHP cosine similarity (pgvector is not
 * available in this environment, mirroring KnowledgeIndexer).
 */
class ClaimIndexer
{
    /**
     * Generate and persist the embedding for a single claim record.
     */
    public function index(ClaimVerification $claim): void
    {
        $text = trim($claim->claim_text.'. '.$claim->explanation);
        $vector = Embeddings::for([$text])->generate()->embeddings[0];

        $claim->forceFill(['embedding' => $vector])->saveQuietly();
    }

    /**
     * Find the most similar active claims to a query string.
     *
     * @return array<int, array{claim_text: string, status: string, explanation: string, region: string|null, similarity: float}>
     */
    public function similar(string $query, int $topK = 3, float $minScore = 0.7): array
    {
        $queryEmbedding = Embeddings::for([$query])->generate()->embeddings[0];

        return ClaimVerification::query()
            ->active()
            ->whereNotNull('embedding')
            ->get()
            ->map(fn (ClaimVerification $c) => [
                'claim_text' => $c->claim_text,
                'status' => $c->status,
                'explanation' => $c->explanation,
                'region' => $c->region,
                'similarity' => $this->cosineSimilarity($queryEmbedding, $c->embedding ?? []),
                'model' => $c,
            ])
            ->filter(fn ($row) => $row['similarity'] >= $minScore)
            ->sortByDesc('similarity')
            ->take($topK)
            ->values()
            ->all();
    }

    /**
     * @param  array<int, float>  $a
     * @param  array<int, float>  $b
     */
    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $val) {
            $dot += $val * ($b[$i] ?? 0);
            $normA += $val * $val;
            $normB += ($b[$i] ?? 0) ** 2;
        }

        $denom = sqrt($normA) * sqrt($normB);

        return $denom > 0 ? $dot / $denom : 0.0;
    }
}
