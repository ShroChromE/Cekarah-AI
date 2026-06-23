<?php

namespace App\Ai\Tools;

use App\Models\KnowledgeDocument;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class CheckInformationFreshnessTool implements Tool
{
    public function description(): string
    {
        return 'Cek apakah sumber informasi dari knowledge base masih relevan atau sudah kadaluarsa (> 6 bulan). Panggil setelah search_knowledge_base untuk dokumen prosedural atau time-sensitive.';
    }

    public function handle(Request $request): string
    {
        $sourceNames = $request['source_names'];
        $staleThreshold = now()->subMonths(6);
        $results = [];

        foreach ($sourceNames as $sourceName) {
            $doc = KnowledgeDocument::where('source_name', 'like', "%{$sourceName}%")
                ->active()
                ->first();

            if (! $doc) {
                continue;
            }

            $isStale = $doc->source_date && $doc->source_date->lt($staleThreshold);

            $results[] = [
                'source_name' => $doc->source_name,
                'source_date' => $doc->source_date?->toDateString(),
                'is_stale' => $isStale,
                'warning' => $isStale
                    ? "Data ini berasal dari {$doc->source_date->format('M Y')}. Verifikasi ke sumber resmi untuk informasi terkini."
                    : null,
            ];
        }

        if (empty($results)) {
            return json_encode(['checked' => false, 'message' => 'Sumber tidak ditemukan di database.'], JSON_UNESCAPED_UNICODE);
        }

        return json_encode(['checked' => true, 'sources' => $results], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'source_names' => $schema->array()->items($schema->string())->required(),
        ];
    }
}
