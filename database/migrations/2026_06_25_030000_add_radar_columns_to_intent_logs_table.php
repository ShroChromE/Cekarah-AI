<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intent_logs', function (Blueprint $table) {
            // Best-effort region extracted from the user message (keyword based),
            // powering the "needs per region" radar. Null when no region is named.
            $table->string('region')->nullable()->after('detected_intent');

            // Marks rows created by the demo seeder so simulated data is never
            // silently mixed with real chat traffic on the radar dashboard.
            $table->boolean('is_simulated')->default(false)->after('needs_review');

            $table->index('region');
            $table->index('is_simulated');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('intent_logs', function (Blueprint $table) {
            $table->dropIndex(['region']);
            $table->dropIndex(['is_simulated']);
            $table->dropIndex(['created_at']);
            $table->dropColumn(['region', 'is_simulated']);
        });
    }
};
