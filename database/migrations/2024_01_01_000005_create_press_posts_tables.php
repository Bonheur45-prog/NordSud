<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('press_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type')->default('press'); // 'press' | 'job' — same shape, filtered per listing
            $table->string('featured_image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('status')->default('draft'); // draft | published
            $table->timestamps();

            $table->index('type');
        });

        Schema::create('press_post_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('press_post_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('excerpt', 500)->nullable();
            $table->longText('body');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['press_post_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('press_post_translations');
        Schema::dropIfExists('press_posts');
    }
};