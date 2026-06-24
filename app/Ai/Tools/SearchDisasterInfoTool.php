<?php

namespace App\Ai\Tools;

use App\Ai\Support\ToolReferences;
use App\Ai\Support\WebResearch;
use App\Models\DisasterEvent;
use App\Services\KnowledgeIndexer;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class SearchDisasterInfoTool implements Tool
{
    public function __construct(
        private readonly KnowledgeIndexer $indexer,
        private readonly WebResearch $web,
    ) {}

    public function name(): string
    {
        return 'search_disaster_info';
    }

    public function description(): string
    {
        return 'Cari informasi resmi tentang bencana atau situasi terkini (mis. "banjir terjadi di mana saja", '
            .'"gempa terbaru di Sumatera"). Gunakan untuk pertanyaan informasi umum bencana. '
            .'Mengembalikan ringkasan kejadian bencana beserta sumber resmi dan tanggalnya.';
    }

    public function handle(Request $request): string
    {
        $query = $request['query'];
        $region = $request['region'] ?? null;
        $type = $request['type'] ?? null;

        $events = DisasterEvent::query()
            ->active()
            ->when($region, fn ($q) => $q->where('region', 'ilike', "%{$region}%"))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->with('sources')
            ->latest('started_at')
            ->limit(5)
            ->get()
            ->map(fn (DisasterEvent $e) => [
                'name' => $e->name,
                'type' => $e->type,
                'region' => $e->region,
                'province' => $e->province,
                'status' => $e->status,
                'severity' => $e->severity,
                'started_at' => $e->started_at?->toDateString(),
                'description' => $e->description,
                'references' => ToolReferences::fromSources($e->sources),
            ])
            ->all();

        // Supplement with the general knowledge base (procedural / contextual info).
        $chunks = $this->indexer->similarChunks($query, 4);

        // Live web research from official sources (null on overload/timeout).
        $webResearch = $this->web->research(
            "Informasi bencana terkini di Indonesia terkait: {$query}. Sebutkan sumber resmi dan tanggal.",
        );

        if (empty($events) && empty($chunks) && $webResearch === null) {
            return json_encode([
                'found' => false,
                'message' => 'Belum ada data resmi bencana yang relevan di basis data Cekarah. Arahkan user ke sumber resmi: bnpb.go.id atau BPBD setempat.',
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'found' => true,
            'events' => $events,
            'knowledge' => array_map(fn ($c) => [
                'text' => $c['chunk_text'],
                'source_name' => $c['source_name'],
                'source_url' => $c['source_url'],
                'is_stale' => $c['is_stale'],
            ], $chunks),
            'web_research' => $webResearch,
            'references' => ToolReferences::fromChunks($chunks),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->required(),
            'region' => $schema->string(),
            'type' => $schema->string()->enum(['flood', 'earthquake', 'landslide', 'tsunami', 'volcanic', 'wildfire']),
        ];
    }
}
