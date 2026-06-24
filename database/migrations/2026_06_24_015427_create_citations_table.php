<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete();
            $table->morphs('citable'); // citable_type + citable_id -> disaster_events, claim_verifications, shelter_locations, aid_programs
            $table->text('quote')->nullable();
            $table->timestamps();

            $table->unique(['source_id', 'citable_type', 'citable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citations');
    }
};
