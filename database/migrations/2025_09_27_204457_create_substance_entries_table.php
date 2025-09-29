<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('substance_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('substance_id')->constrained()->onDelete('cascade');

            // Substance-specific tracking data
            $table->string('dosage')->nullable();
            $table->datetime('taken_at')->nullable(); // When they took this substance
            $table->integer('duration_minutes')->nullable(); // How long effects lasted

            // Ratings 1-5 for this specific substance
            $table->tinyInteger('focus_rating')->nullable();
            $table->tinyInteger('mood_rating')->nullable();
            $table->tinyInteger('sleep_rating')->nullable();
            $table->tinyInteger('effectiveness_rating')->nullable(); // Overall effectiveness

            $table->text('side_effects')->nullable();
            $table->text('notes')->nullable(); // Substance-specific notes

            $table->timestamps();

            $table->unique(['journal_entry_id', 'substance_id']);
            $table->index(['substance_id', 'taken_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substance_entries');
    }
};
