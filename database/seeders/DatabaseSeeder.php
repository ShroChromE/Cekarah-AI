<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(EmergencyContactSeeder::class);
        $this->call(KnowledgeSeeder::class);
        $this->call(DatasetSeeder::class);

        // Generate embeddings for the knowledge base so disaster-info RAG works
        // immediately after a fresh seed (otherwise knowledge_chunks stays empty).
        $this->command?->info('Indexing knowledge base (embeddings)…');
        Artisan::call('knowledge:index');
        $this->command?->line(trim(Artisan::output()));

        // Clearly-labelled simulated chat logs so Radar Tren is representative
        // for the demo (is_simulated = true; never mixed with live traffic).
        $this->call(RadarSimulationSeeder::class);
    }
}
