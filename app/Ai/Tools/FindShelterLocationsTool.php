<?php

namespace App\Ai\Tools;

use App\Ai\Support\ToolReferences;
use App\Ai\Support\WebResearch;
use App\Models\ShelterLocation;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class FindShelterLocationsTool implements Tool
{
    public function __construct(private readonly WebResearch $web) {}

    public function name(): string
    {
        return 'find_shelter_locations';
    }

    public function description(): string
    {
        return 'Cari lokasi posko pengungsian, shelter, dapur umum, atau pos kesehatan di suatu wilayah '
            .'(mis. "posko pengungsian di Binjai di mana?"). Mengembalikan daftar lokasi lengkap dengan '
            .'alamat, koordinat (latitude/longitude), kapasitas, dan sumber — untuk ditampilkan di peta.';
    }

    public function handle(Request $request): string
    {
        $region = $request['region'];
        $type = $request['type'] ?? null;

        $locations = ShelterLocation::query()
            ->active()
            ->where(function ($q) use ($region) {
                $q->where('region', 'ilike', "%{$region}%")
                    ->orWhere('address', 'ilike', "%{$region}%");
            })
            ->when($type, fn ($q) => $q->where('type', $type))
            ->with('sources')
            ->limit(20)
            ->get()
            ->map(fn (ShelterLocation $s) => [
                'name' => $s->name,
                'type' => $s->type,
                'address' => $s->address,
                'region' => $s->region,
                'latitude' => $s->latitude,
                'longitude' => $s->longitude,
                'capacity' => $s->capacity,
                'occupancy' => $s->occupancy,
                'contact' => $s->contact,
                'notes' => $s->notes,
                'references' => ToolReferences::fromSources($s->sources),
            ])
            ->all();

        // Live web context (coordinates for the map still come only from the DB).
        $webResearch = $this->web->research(
            "Informasi posko pengungsian, shelter, atau dapur umum terkini untuk wilayah {$region} akibat bencana. Sebutkan sumber resmi (BPBD/BNPB) dan tanggal.",
        );

        if (empty($locations) && $webResearch === null) {
            return json_encode([
                'found' => false,
                'message' => "Belum ada data posko/shelter resmi untuk wilayah \"{$region}\" di basis data Cekarah. "
                    .'Sarankan user menghubungi BPBD setempat atau call center BNPB 117.',
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode([
            'found' => ! empty($locations),
            'count' => count($locations),
            'locations' => $locations,
            'web_research' => $webResearch,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'region' => $schema->string()->required(),
            'type' => $schema->string()->enum(['evacuation_shelter', 'public_kitchen', 'health_post', 'logistics_post']),
        ];
    }
}
