<?php

namespace Database\Seeders;

use App\Models\IntentLog;
use App\Services\RegionExtractor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeds CLEARLY-LABELLED simulated chat logs (is_simulated = true) so the Radar
 * Tren dashboard looks representative during a demo, without claiming the rows
 * are real users. Safe to run repeatedly: it clears its own simulated rows first
 * and never touches live traffic.
 *
 *   php artisan db:seed --class=RadarSimulationSeeder
 */
class RadarSimulationSeeder extends Seeder
{
    /**
     * Claim clusters: each entry is paraphrases of the same underlying claim, plus
     * a per-day weight curve over the last 14 days (oldest → newest). A rising
     * tail produces a "surging" signal on the radar.
     *
     * @var array<int, array{variants: array<int, string>, curve: array<int, int>}>
     */
    private const CLAIM_CLUSTERS = [
        [
            'variants' => [
                'Apakah benar air laut naik di Pidie Jaya?',
                'Katanya air laut akan naik dan tenggelamkan Pidie Jaya, benar?',
                'Air laut naik Pidie Jaya hoaks atau fakta?',
                'Info air laut naik di pesisir Pidie Jaya benarkah?',
            ],
            // Strong recent surge.
            'curve' => [0, 0, 1, 0, 1, 1, 1, 2, 1, 2, 3, 4, 5, 6],
        ],
        [
            'variants' => [
                'Benarkah akan ada gempa besar susulan malam ini?',
                'Katanya ada gempa megathrust malam ini, benar?',
                'Isu gempa besar susulan apakah valid?',
            ],
            'curve' => [1, 0, 1, 1, 0, 1, 0, 1, 2, 1, 2, 2, 3, 3],
        ],
        [
            'variants' => [
                'Apakah bendungan akan jebol besok?',
                'Kabar bendungan jebol benar tidak?',
                'Info bendungan jebol hoaks?',
            ],
            // Flat / fading — should NOT be flagged surging.
            'curve' => [2, 2, 1, 2, 1, 1, 1, 0, 1, 0, 1, 0, 0, 1],
        ],
        [
            'variants' => [
                'Benarkah air PDAM tercemar zat berbahaya?',
                'Katanya air ledeng beracun pasca banjir, benar?',
            ],
            'curve' => [0, 0, 0, 0, 1, 0, 1, 1, 1, 2, 2, 3, 3, 4],
        ],
    ];

    /**
     * Region-need messages (shelter/aid). Each region has an intent mix + curve.
     *
     * @var array<int, array{region_phrase: string, intent: string, variants: array<int, string>, curve: array<int, int>}>
     */
    private const REGION_NEEDS = [
        [
            'region_phrase' => 'Binjai',
            'intent' => 'shelter_location',
            'variants' => ['Posko pengungsian di Binjai di mana?', 'Tempat mengungsi terdekat Binjai?', 'Lokasi posko banjir Binjai?'],
            'curve' => [0, 1, 1, 1, 2, 2, 2, 3, 3, 4, 5, 5, 6, 7],
        ],
        [
            'region_phrase' => 'Binjai',
            'intent' => 'aid_assistance',
            'variants' => ['Bantuan sosial untuk korban banjir Binjai apa saja?', 'Ada bansos di Binjai tidak?'],
            'curve' => [0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 4, 4, 5, 5],
        ],
        [
            'region_phrase' => 'Medan',
            'intent' => 'shelter_location',
            'variants' => ['Posko pengungsian di Medan?', 'Tempat evakuasi terdekat Medan?'],
            'curve' => [1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 0, 1],
        ],
        [
            'region_phrase' => 'Pidie Jaya',
            'intent' => 'aid_assistance',
            'variants' => ['Bantuan untuk warga Pidie Jaya apa saja?', 'Bansos korban bencana Pidie Jaya?'],
            'curve' => [0, 0, 0, 1, 0, 1, 1, 1, 2, 2, 2, 3, 3, 4],
        ],
        [
            'region_phrase' => 'Langkat',
            'intent' => 'shelter_location',
            'variants' => ['Posko di Langkat ada di mana?', 'Lokasi pengungsian Langkat?'],
            'curve' => [0, 0, 1, 0, 1, 0, 1, 1, 1, 1, 2, 2, 2, 3],
        ],
    ];

    private const WINDOW_DAYS = 14;

    public function run(): void
    {
        $extractor = new RegionExtractor;

        IntentLog::where('is_simulated', true)->delete();

        foreach (self::CLAIM_CLUSTERS as $cluster) {
            $this->emit($cluster['variants'], $cluster['curve'], 'claim_verification', 'verify_claim', $extractor);
        }

        foreach (self::REGION_NEEDS as $need) {
            $tool = $need['intent'] === 'shelter_location' ? 'find_shelter_locations' : 'get_aid_assistance_info';
            $this->emit($need['variants'], $need['curve'], $need['intent'], $tool, $extractor);
        }

        $total = IntentLog::where('is_simulated', true)->count();
        $this->command?->info("Seeded {$total} simulated radar log rows (is_simulated = true).");
    }

    /**
     * Materialise one curve into individual simulated log rows.
     *
     * @param  array<int, string>  $variants
     * @param  array<int, int>  $curve  per-day counts, oldest → newest
     */
    private function emit(array $variants, array $curve, string $intent, string $tool, RegionExtractor $extractor): void
    {
        foreach ($curve as $dayOffset => $count) {
            $daysAgo = self::WINDOW_DAYS - 1 - $dayOffset;

            for ($i = 0; $i < $count; $i++) {
                $message = $variants[array_rand($variants)];
                $at = Carbon::today()->subDays($daysAgo)->addHours(random_int(6, 21))->addMinutes(random_int(0, 59));

                IntentLog::create([
                    'conversation_id' => 'sim-'.uniqid(),
                    'user_message' => $message,
                    'detected_intent' => $intent,
                    'region' => $extractor->extract($message),
                    'tool_called' => $tool,
                    'needs_review' => false,
                    'is_simulated' => true,
                    'confidence' => round(random_int(75, 97) / 100, 2),
                    'created_at' => $at,
                    'updated_at' => $at,
                ]);
            }
        }
    }
}
