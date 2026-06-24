<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shelter_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_event_id')->nullable()->constrained('disaster_events')->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('evacuation_shelter'); // evacuation_shelter | public_kitchen | health_post | logistics_post
            $table->text('address');
            $table->string('region'); // city / kabupaten, e.g. "Binjai"
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('capacity')->nullable();
            $table->integer('occupancy')->nullable();
            $table->string('contact')->nullable();
            $table->text('notes')->nullable();
            $table->jsonb('embedding')->nullable(); // reserved for future semantic search (not populated yet)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['region', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelter_locations');
    }
};
