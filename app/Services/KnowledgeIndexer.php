<?php

namespace App\Services;

use App\Models\KnowledgeChunk;
use App\Models\KnowledgeDocument;
use Laravel\Ai\Embeddings;

class KnowledgeIndexer
{
    public function chunkText(string $text, int $size = 400, int $overlap = 50): array
    {
        $words = preg_split('/\s+/', trim($text));
        $chunks = [];
        $i = 0;

        while ($i < count($words)) {
            $chunks[] = implode(' ', array_slice($words, $i, $size));
            $i += max(1, $size - $overlap);
        }

        return array_values(array_filter($chunks));
    }

    public function index(KnowledgeDocument $document): void
    {
        KnowledgeChunk::where('document_id', $document->id)->delete();

        $textChunks = $this->chunkText($document->content);

        $response = Embeddings::for($textChunks)->generate();

        foreach ($response->embeddings as $i => $vector) {
            KnowledgeChunk::create([
                'document_id' => $document->id,
                'chunk_text' => $textChunks[$i],
                'chunk_index' => $i,
                'embedding' => $vector,
            ]);
        }

        $document->update(['indexed_at' => now()]);
    }

    /**
     * Find the top-K most similar chunks to a query string using cosine similarity.
     *
     * @return array<int, array{chunk_text: string, title: string, source_name: string, source_url: string, is_stale: bool, similarity: float}>
     */
    public function similarChunks(string $query, int $topK = 5): array
    {
        $queryEmbedding = Embeddings::for([$query])->generate()->embeddings[0];

        $chunks = KnowledgeChunk::with('document')->get();

        $scored = $chunks
            ->filter(fn ($c) => ! empty($c->embedding))
            ->map(fn ($c) => [
                'chunk_text' => $c->chunk_text,
                'title' => $c->document->title,
                'source_name' => $c->document->source_name,
                'source_url' => $c->document->source_url,
                'is_stale' => $c->document->source_date?->lt(now()->subMonths(6)) ?? false,
                'similarity' => $this->cosineSimilarity($queryEmbedding, $c->embedding),
            ])
            ->sortByDesc('similarity')
            ->take($topK)
            ->values();

        return $scored->toArray();
    }

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
