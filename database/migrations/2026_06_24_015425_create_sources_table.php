<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('publisher')->nullable();
            $table->string('source_type')->default('official'); // official | media | fact_check | synthetic
            $table->date('published_at')->nullable();
            $table->boolean('is_simulated')->default(true); // transparency flag for Responsible AI
            $table->timestamps();

            $table->index('source_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
