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
        Schema::create('substance_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('substance_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type'); // 'study', 'article', 'review', 'book', 'video', 'website'
            $table->text('url')->nullable();
            $table->string('authors')->nullable();
            $table->string('publication')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('doi')->nullable(); // Digital Object Identifier for academic papers
            $table->text('abstract')->nullable();
            $table->json('tags')->nullable(); // searchable tags like 'memory', 'focus', 'safety'
            $table->integer('quality_score')->nullable(); // 1-10 rating for study quality
            $table->text('key_findings')->nullable();
            $table->boolean('is_verified')->default(false); // admin verified
            $table->foreignId('added_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['substance_id', 'type']);
            $table->index(['type', 'quality_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substance_resources');
    }
};
