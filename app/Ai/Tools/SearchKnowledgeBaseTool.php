<?php

namespace App\Ai\Tools;

use App\Services\KnowledgeIndexer;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class SearchKnowledgeBaseTool implements Tool
{
    public function __construct(private readonly KnowledgeIndexer $indexer) {}

    public function description(): string
    {
        return 'Cari informasi dari knowledge base resmi Cekarah (BNPB, Kemensos, PMI, BMKG). '
            .'Gunakan untuk mencari prosedur bantuan, kontak darurat, '
            .'atau data untuk memverifikasi klaim yang beredar di masyarakat.';
    }

    public function handle(Request $request): string
    {
        $topK = $request['top_k'] ?? 5;
        $results = $this->indexer->similarChunks($request['query'], $topK);

        if (empty($results)) {
            return json_encode([
                'found' => false,
                'message' => 'Tidak ada informasi relevan ditemukan di knowledge base. Arahkan ke sumber resmi: bnpb.go.id, bmkg.go.id, atau 117 ext 7.',
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'found' => true,
            'results' => array_map(fn ($r) => [
                'chunk_text' => $r['chunk_text'],
                'title' => $r['title'],
                'source_name' => $r['source_name'],
                'source_url' => $r['source_url'],
                'is_stale' => $r['is_stale'],
            ], $results),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->required(),
            'top_k' => $schema->integer()->min(1)->max(10),
        ];
    }
}
