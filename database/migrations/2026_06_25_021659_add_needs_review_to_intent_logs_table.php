<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intent_logs', function (Blueprint $table) {
            // Set true when a tool returned a fallback ("belum ada data resmi"),
            // surfacing the message in the volunteer review queue.
            $table->boolean('needs_review')->default(false)->after('tool_called');
            $table->index('needs_review');
        });
    }

    public function down(): void
    {
        Schema::table('intent_logs', function (Blueprint $table) {
            $table->dropIndex(['needs_review']);
            $table->dropColumn('needs_review');
        });
    }
};
