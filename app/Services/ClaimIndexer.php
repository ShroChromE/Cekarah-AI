<?php

namespace App\Services;

use App\Models\ClaimVerification;
use Laravel\Ai\Embeddings;

/**
 * Keeps claim_verifications retrievable by the verify_claim tool: generates an
 * embedding for each claim and runs pgvector cosine similarity (`<=>`).
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
        $queryEmbedding = Embeddings::for([$query])->cache()->generate()->embeddings[0];
        $literal = '['.implode(',', $queryEmbedding).']';

        return ClaimVerification::query()
            ->active()
            ->whereNotNull('embedding')
            ->select('claim_verifications.*')
            ->selectRaw('1 - (embedding <=> ?::vector) AS similarity', [$literal])
            ->orderByRaw('embedding <=> ?::vector', [$literal])
            ->limit($topK)
            ->get()
            ->map(fn (ClaimVerification $c) => [
                'claim_text' => $c->claim_text,
                'status' => $c->status,
                'explanation' => $c->explanation,
                'region' => $c->region,
                'similarity' => (float) $c->similarity,
                'model' => $c,
            ])
            ->filter(fn ($row) => $row['similarity'] >= $minScore)
            ->values()
            ->all();
    }
}
