<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disaster_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // flood | earthquake | landslide | tsunami | volcanic | wildfire
            $table->string('region'); // city / kabupaten, e.g. "Binjai"
            $table->string('province')->nullable();
            $table->string('status')->default('active'); // active | recovery | closed
            $table->string('severity')->nullable(); // waspada | siaga | awas
            $table->date('started_at')->nullable();
            $table->text('description');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->jsonb('embedding')->nullable(); // RAG semantic search (jsonb; pgvector unavailable)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('region');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disaster_events');
    }
};
