<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intent_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->nullable()->constrained('chat_sessions')->nullOnDelete();
            $table->string('conversation_id')->nullable();
            $table->text('user_message');
            $table->string('detected_intent'); // disaster_info | claim_verification | shelter_location | aid_assistance | out_of_scope
            $table->string('tool_called')->nullable(); // null for out_of_scope
            $table->decimal('confidence', 3, 2)->nullable();
            $table->timestamps();

            $table->index('detected_intent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intent_logs');
    }
};
