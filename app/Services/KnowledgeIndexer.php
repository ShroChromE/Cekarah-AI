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
     * Find the top-K most similar chunks to a query string using pgvector cosine
     * distance (`<=>`). similarity = 1 - cosine_distance.
     *
     * @return array<int, array{chunk_text: string, title: string, source_name: string, source_url: string, is_stale: bool, similarity: float}>
     */
    public function similarChunks(string $query, int $topK = 5): array
    {
        $queryEmbedding = Embeddings::for([$query])->generate()->embeddings[0];
        $literal = '['.implode(',', $queryEmbedding).']';

        return KnowledgeChunk::query()
            ->whereNotNull('embedding')
            ->with('document')
            ->select('knowledge_chunks.*')
            ->selectRaw('1 - (embedding <=> ?::vector) AS similarity', [$literal])
            ->orderByRaw('embedding <=> ?::vector', [$literal])
            ->limit($topK)
            ->get()
            ->map(fn (KnowledgeChunk $c) => [
                'chunk_text' => $c->chunk_text,
                'title' => $c->document->title,
                'source_name' => $c->document->source_name,
                'source_url' => $c->document->source_url,
                'is_stale' => $c->document->source_date?->lt(now()->subMonths(6)) ?? false,
                'similarity' => (float) $c->similarity,
            ])
            ->all();
    }
}
