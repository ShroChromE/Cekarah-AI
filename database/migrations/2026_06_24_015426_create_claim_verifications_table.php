<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_event_id')->nullable()->constrained('disaster_events')->nullOnDelete();
            $table->text('claim_text');
            $table->string('status'); // verified | unverified | hoax | no_official_data
            $table->text('explanation');
            $table->string('region')->nullable();
            $table->jsonb('embedding')->nullable(); // match user claims to known patterns (RAG)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_verifications');
    }
};
