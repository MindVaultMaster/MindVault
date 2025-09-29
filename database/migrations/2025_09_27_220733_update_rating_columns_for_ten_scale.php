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
        Schema::table('journal_entries', function (Blueprint $table) {
            // Change tinyInteger columns to smallInteger to support 1-10 scale
            $table->smallInteger('overall_focus')->nullable()->change();
            $table->smallInteger('overall_mood')->nullable()->change();
            $table->smallInteger('overall_sleep')->nullable()->change();
            $table->smallInteger('overall_energy')->nullable()->change();
        });

        Schema::table('substance_entries', function (Blueprint $table) {
            // Also update substance entry ratings
            $table->smallInteger('focus_rating')->nullable()->change();
            $table->smallInteger('mood_rating')->nullable()->change();
            $table->smallInteger('sleep_rating')->nullable()->change();
            $table->smallInteger('effectiveness_rating')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            // Revert back to tinyInteger (1-5 scale)
            $table->tinyInteger('overall_focus')->nullable()->change();
            $table->tinyInteger('overall_mood')->nullable()->change();
            $table->tinyInteger('overall_sleep')->nullable()->change();
            $table->tinyInteger('overall_energy')->nullable()->change();
        });

        Schema::table('substance_entries', function (Blueprint $table) {
            $table->tinyInteger('focus_rating')->nullable()->change();
            $table->tinyInteger('mood_rating')->nullable()->change();
            $table->tinyInteger('sleep_rating')->nullable()->change();
            $table->tinyInteger('effectiveness_rating')->nullable()->change();
        });
    }
};
