<?php

namespace Database\Seeders;

use App\Models\IntentLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeds SIMULATED chat intent logs (is_simulated = true) so the Radar Tren
 * dashboard has representative data to demo. These are NOT real user
 * interactions — every row is flagged is_simulated so the radar can label them
 * honestly and keep them separable from live traffic.
 *
 * Re-runnable: clears prior simulated rows first.
 *
 * Run with: php artisan db:seed --class=TrendLogSeeder
 */
class TrendLogSeeder extends Seeder
{
    /**
     * Hoax-claim themes. Each phrasing shares enough keywords to cluster, and
     * the per-day weights (oldest→newest over 7 days) shape the trend; the first
     * theme deliberately surges in the recent half.
     *
     * @var array<int, array{intent: string, phrasings: array<int, string>, weights: array<int, int>}>
     */
    private array $claims = [
        [
            'intent' => 'claim_verification',
            'phrasings' => [
                'Benarkah bendungan akan jebol malam ini?',
                'Kabar bendungan jebol di hulu sungai, benar tidak?',
                'Viral pesan bendungan jebol akan banjiri kota, ini valid?',
                'Tolong cek info bendungan jebol yang disebar di grup WhatsApp',
                'Apakah benar pintu air bendungan dibuka dan bendungan jebol?',
            ],
            'weights' => [0, 1, 0, 2, 3, 5, 6], // surging at the end
        ],
        [
            'intent' => 'claim_verification',
            'phrasings' => [
                'Benarkah air laut naik dan akan terjadi tsunami?',
                'Kabar air laut naik di pesisir, benar atau hoaks?',
                'Viral peringatan tsunami malam ini, ini valid?',
            ],
            'weights' => [2, 1, 2, 1, 2, 1, 1],
        ],
        [
            'intent' => 'claim_verification',
            'phrasings' => [
                'Apakah akan ada gempa susulan dahsyat malam ini?',
                'Kabar gempa susulan besar dari orang dalam BMKG, benar?',
                'Info gempa susulan magnitudo besar malam ini valid tidak?',
            ],
            'weights' => [1, 1, 1, 1, 1, 1, 2],
        ],
        [
            'intent' => 'claim_verification',
            'phrasings' => [
                'Benarkah nomor rekening donasi korban banjir ini resmi?',
                'Cek rekening donasi bencana yang disebar, asli atau palsu?',
            ],
            'weights' => [1, 0, 1, 0, 1, 0, 1],
        ],
    ];

    /**
     * Per-region needs (shelter + aid). First region surges recently.
     *
     * @var array<int, array{region: string, phrasings: array<int, array{intent: string, text: string}>, weights: array<int, int>}>
     */
    private array $regions = [
        [
            'region' => 'Binjai',
            'phrasings' => [
                ['intent' => 'shelter_location', 'text' => 'Posko pengungsian di Binjai di mana?'],
                ['intent' => 'shelter_location', 'text' => 'Lokasi dapur umum untuk korban banjir Binjai?'],
                ['intent' => 'aid_assistance', 'text' => 'Bantuan apa yang tersedia untuk korban banjir di Binjai?'],
            ],
            'weights' => [1, 1, 1, 2, 3, 4, 5], // surging
        ],
        [
            'region' => 'Langkat',
            'phrasings' => [
                ['intent' => 'shelter_location', 'text' => 'Di mana posko pengungsian di Langkat?'],
                ['intent' => 'aid_assistance', 'text' => 'Bansos korban banjir Langkat apa saja?'],
            ],
            'weights' => [1, 1, 2, 1, 1, 2, 1],
        ],
        [
            'region' => 'Deli Serdang',
            'phrasings' => [
                ['intent' => 'shelter_location', 'text' => 'Posko bencana di Deli Serdang lokasinya?'],
                ['intent' => 'aid_assistance', 'text' => 'Bantuan logistik untuk Deli Serdang?'],
            ],
            'weights' => [1, 0, 1, 1, 1, 1, 2],
        ],
        [
            'region' => 'Medan',
            'phrasings' => [
                ['intent' => 'shelter_location', 'text' => 'Tempat pengungsian banjir di Medan?'],
            ],
            'weights' => [1, 1, 0, 1, 0, 1, 1],
        ],
    ];

    public function run(): void
    {
        IntentLog::where('is_simulated', true)->delete();

        $rows = [];
        $days = 7;

        foreach ($this->claims as $theme) {
            $this->buildSeries($rows, $days, $theme['weights'], fn () => [
                'detected_intent' => $theme['intent'],
                'tool_called' => 'verify_claim',
                'user_message' => $theme['phrasings'][array_rand($theme['phrasings'])],
                'region' => null,
            ]);
        }

        foreach ($this->regions as $area) {
            $this->buildSeries($rows, $days, $area['weights'], function () use ($area) {
                $p = $area['phrasings'][array_rand($area['phrasings'])];

                return [
                    'detected_intent' => $p['intent'],
                    'tool_called' => $p['intent'] === 'shelter_location' ? 'find_shelter_locations' : 'get_aid_assistance_info',
                    'user_message' => $p['text'],
                    'region' => $area['region'],
                ];
            });
        }

        IntentLog::insert($rows);

        $this->command?->info('Seeded '.count($rows).' simulated intent logs for the Radar Tren demo.');
    }

    /**
     * Append `weights[d]` rows for each day d (0 = oldest) using the row factory.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<int, int>  $weights
     * @param  callable(): array<string, mixed>  $make
     */
    private function buildSeries(array &$rows, int $days, array $weights, callable $make): void
    {
        for ($d = 0; $d < $days; $d++) {
            $count = $weights[$d] ?? 0;

            for ($n = 0; $n < $count; $n++) {
                $createdAt = Carbon::today()
                    ->subDays($days - 1 - $d)
                    ->addHours(random_int(7, 21))
                    ->addMinutes(random_int(0, 59));

                $rows[] = [
                    ...$make(),
                    'is_simulated' => true,
                    'needs_review' => false,
                    'confidence' => round(random_int(70, 98) / 100, 2),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
        }
    }
}
