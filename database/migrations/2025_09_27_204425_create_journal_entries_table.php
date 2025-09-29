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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('content')->nullable(); // General journal notes
            $table->date('entry_date');
            $table->time('entry_time')->nullable();

            // Overall daily ratings (optional)
            $table->tinyInteger('overall_focus')->nullable(); // 1-5
            $table->tinyInteger('overall_mood')->nullable(); // 1-5
            $table->tinyInteger('overall_sleep')->nullable(); // 1-5
            $table->tinyInteger('overall_energy')->nullable(); // 1-5

            $table->text('general_notes')->nullable();
            $table->boolean('is_public')->default(false); // Can be shared with community
            $table->timestamps();

            $table->index(['user_id', 'entry_date']);
            $table->index(['is_public', 'entry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
