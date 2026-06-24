<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeds the intent-category dataset: sources, disaster events, claim
 * verifications, shelter locations, and aid programs.
 *
 * Skeleton only — populated in Fase 4 with synthetic-but-sourced data.
 */
class DatasetSeeder extends Seeder
{
    public function run(): void
    {
        // Fase 4 will populate, in dependency order:
        // 1. sources (reusable citations)
        // 2. disaster_events (hub)
        // 3. claim_verifications, shelter_locations, aid_programs (link to events)
        // 4. attach citations (polymorphic) to each record
        // 5. generate embeddings for disaster_events & claim_verifications
    }
}
