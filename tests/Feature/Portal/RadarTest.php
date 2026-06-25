<?php

namespace Tests\Feature\Portal;

use App\Models\IntentLog;
use App\Models\User;
use App\Services\RadarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RadarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_the_radar(): void
    {
        $this->get('/portal/radar')->assertRedirect(route('login'));
    }

    public function test_volunteer_can_view_the_radar_page(): void
    {
        $user = User::factory()->create(['role' => 'volunteer']);

        $this->withoutVite()
            ->actingAs($user)
            ->get('/portal/radar')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('portal/Radar')
                ->has('claimTrends')
                ->has('regionNeeds')
                ->has('meta'),
            );
    }

    public function test_similar_claims_are_clustered_together(): void
    {
        $variants = [
            'Apakah benar air laut naik di Pidie Jaya?',
            'Katanya air laut akan naik di Pidie Jaya, benar?',
            'Air laut naik Pidie Jaya hoaks?',
        ];

        foreach ($variants as $message) {
            IntentLog::factory()->create([
                'detected_intent' => 'claim_verification',
                'user_message' => $message,
                'created_at' => now()->subDay(),
            ]);
        }

        IntentLog::factory()->create([
            'detected_intent' => 'claim_verification',
            'user_message' => 'Benarkah jembatan runtuh akibat gempa?',
            'created_at' => now()->subDay(),
        ]);

        $trends = app(RadarService::class)->claimTrends(7);

        $this->assertCount(2, $trends);
        $this->assertSame(3, $trends[0]['total']); // the air-laut cluster, biggest first
    }

    public function test_region_needs_group_by_region_and_flag_surges(): void
    {
        // Four recent Binjai shelter questions → a surge signal.
        IntentLog::factory()->count(4)->create([
            'detected_intent' => 'shelter_location',
            'region' => 'Binjai',
            'created_at' => now(),
        ]);

        $needs = app(RadarService::class)->regionNeeds(7);

        $this->assertCount(1, $needs);
        $this->assertSame('Binjai', $needs[0]['label']);
        $this->assertTrue($needs[0]['is_surging']);
    }

    public function test_live_source_excludes_simulated_rows(): void
    {
        IntentLog::factory()->create([
            'detected_intent' => 'claim_verification',
            'user_message' => 'Apakah benar tsunami datang malam ini?',
            'is_simulated' => true,
            'created_at' => now(),
        ]);

        $this->assertCount(1, app(RadarService::class)->claimTrends(7, 'all'));
        $this->assertCount(0, app(RadarService::class)->claimTrends(7, 'live'));
    }
}
