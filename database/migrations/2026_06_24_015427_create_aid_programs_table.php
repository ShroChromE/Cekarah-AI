<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aid_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_event_id')->nullable()->constrained('disaster_events')->nullOnDelete();
            $table->string('name');
            $table->string('provider'); // e.g. "Kemensos", "BNPB", "PMI"
            $table->string('aid_type'); // cash | food | logistics | health | shelter
            $table->text('description');
            $table->string('region'); // city / kabupaten, e.g. "Binjai"
            $table->text('eligibility')->nullable();
            $table->string('schedule_status')->default('planned'); // planned | ongoing | distributed | closed
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->jsonb('embedding')->nullable(); // reserved for future semantic search (not populated yet)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['region', 'aid_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aid_programs');
    }
};
