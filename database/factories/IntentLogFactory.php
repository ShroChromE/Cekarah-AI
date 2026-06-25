<?php

namespace Database\Factories;

use App\Models\IntentLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IntentLog>
 */
class IntentLogFactory extends Factory
{
    protected $model = IntentLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $intent = fake()->randomElement([
            'disaster_info', 'claim_verification', 'shelter_location', 'aid_assistance', 'out_of_scope',
        ]);

        $tool = match ($intent) {
            'disaster_info' => 'search_disaster_info',
            'claim_verification' => 'verify_claim',
            'shelter_location' => 'find_shelter_locations',
            'aid_assistance' => 'get_aid_assistance_info',
            default => null,
        };

        return [
            'conversation_id' => fake()->uuid(),
            'user_message' => fake()->sentence(),
            'detected_intent' => $intent,
            'region' => null,
            'tool_called' => $tool,
            'needs_review' => false,
            'is_simulated' => false,
            'confidence' => fake()->randomFloat(2, 0.6, 0.99),
        ];
    }

    public function simulated(): static
    {
        return $this->state(['is_simulated' => true]);
    }
}
