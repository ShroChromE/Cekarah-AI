<?php

namespace App\Ai\Tools;

use App\Ai\Support\ToolReferences;
use App\Ai\Support\WebResearch;
use App\Models\AidProgram;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class GetAidAssistanceInfoTool implements Tool
{
    public function __construct(private readonly WebResearch $web) {}

    public function name(): string
    {
        return 'get_aid_assistance_info';
    }

    public function description(): string
    {
        return 'Cari program bantuan sosial/bansos yang tersedia di suatu wilayah atau kondisi bencana '
            .'(mis. "daerah saya di Binjai kena bencana, ada bantuan apa?"). Mengembalikan daftar program '
            .'bantuan: penyedia, jenis bantuan, status/jadwal penyaluran, syarat singkat, dan sumber resmi.';
    }

    public function handle(Request $request): string
    {
        $region = $request['region'] ?? null;
        $aidType = $request['aid_type'] ?? null;

        $programs = AidProgram::query()
            ->active()
            ->when($region, fn ($q) => $q->where('region', 'ilike', "%{$region}%"))
            ->when($aidType, fn ($q) => $q->where('aid_type', $aidType))
            ->with('sources')
            ->limit(15)
            ->get()
            ->map(fn (AidProgram $p) => [
                'name' => $p->name,
                'provider' => $p->provider,
                'aid_type' => $p->aid_type,
                'description' => $p->description,
                'region' => $p->region,
                'eligibility' => $p->eligibility,
                'schedule_status' => $p->schedule_status,
                'starts_at' => $p->starts_at?->toDateString(),
                'ends_at' => $p->ends_at?->toDateString(),
                'references' => ToolReferences::fromSources($p->sources),
            ])
            ->all();

        // Live web research for current aid/bansos programs in the region.
        $scope = $region ?? 'wilayah terdampak bencana';
        $webResearch = $this->web->research(
            "Program bantuan sosial/bansos atau bantuan bencana terkini untuk {$scope} di Indonesia. Sebutkan penyedia (Kemensos/BNPB/pemda), jenis bantuan, sumber resmi, dan tanggal.",
        );

        if (empty($programs) && $webResearch === null) {
            return json_encode([
                'found' => false,
                'message' => 'Belum ada data program bantuan resmi yang relevan di basis data Cekarah. Sarankan user '
                    .'mengecek aplikasi Cek Bansos (cekbansos.kemensos.go.id) atau menghubungi kantor desa/kelurahan.',
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'found' => ! empty($programs),
            'count' => count($programs),
            'programs' => $programs,
            'web_research' => $webResearch,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'region' => $schema->string(),
            'aid_type' => $schema->string()->enum(['cash', 'food', 'logistics', 'health', 'shelter']),
        ];
    }
}
