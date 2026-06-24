<?php

namespace App\Ai\Tools;

use App\Ai\Support\ToolReferences;
use App\Ai\Support\WebResearch;
use App\Models\ClaimVerification;
use App\Services\KnowledgeIndexer;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class VerifyClaimTool implements Tool
{
    public function __construct(
        private readonly KnowledgeIndexer $indexer,
        private readonly WebResearch $web,
    ) {}

    public function name(): string
    {
        return 'verify_claim';
    }

    public function description(): string
    {
        return 'Verifikasi kebenaran sebuah klaim/kabar yang disampaikan user (mis. "kata teman saya akan ada '
            .'banjir besar hari ini, benar tidak?"). Mengembalikan status (verified, unverified, hoax, '
            .'no_official_data), penjelasan, dan referensi resmi. JANGAN memvonis tanpa rujukan.';
    }

    public function handle(Request $request): string
    {
        $claim = $request['claim'];

        // Structured records of known claims/hoax patterns (populated in Fase 4).
        $records = ClaimVerification::query()
            ->active()
            ->where('claim_text', 'ilike', '%'.substr($claim, 0, 40).'%')
            ->with('sources')
            ->limit(3)
            ->get()
            ->map(fn (ClaimVerification $c) => [
                'claim_text' => $c->claim_text,
                'status' => $c->status,
                'explanation' => $c->explanation,
                'references' => ToolReferences::fromSources($c->sources),
            ])
            ->all();

        // Supplement with knowledge base hoax-pattern / official-channel docs.
        $chunks = $this->indexer->similarChunks($claim, 4);

        // Live web cross-check against fact-checkers and official channels.
        $webResearch = $this->web->research(
            "Verifikasi kebenaran klaim/kabar berikut dengan sumber terkini (BMKG, BNPB, MAFINDO/turnbackhoax, media kredibel): \"{$claim}\". Apakah benar, belum terverifikasi, atau hoaks? Sebutkan sumber dan tanggal.",
        );

        if (empty($records) && empty($chunks) && $webResearch === null) {
            return json_encode([
                'status' => 'no_official_data',
                'message' => 'Belum ada data resmi untuk memverifikasi klaim ini. Sarankan user mengecek langsung ke '
                    .'BMKG (bmkg.go.id) atau BNPB, dan tidak menyebarkan informasi yang belum terverifikasi.',
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'matched_records' => $records,
            'knowledge' => array_map(fn ($c) => [
                'text' => $c['chunk_text'],
                'source_name' => $c['source_name'],
                'source_url' => $c['source_url'],
                'is_stale' => $c['is_stale'],
            ], $chunks),
            'web_research' => $webResearch,
            'references' => ToolReferences::fromChunks($chunks),
            'guidance' => 'Tentukan status berdasarkan rujukan di atas (termasuk web_research bila ada). Jika tidak ada '
                .'rujukan resmi yang mendukung, gunakan status no_official_data dan jelaskan ciri-ciri pola hoaks bila relevan.',
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'claim' => $schema->string()->required(),
        ];
    }
}
