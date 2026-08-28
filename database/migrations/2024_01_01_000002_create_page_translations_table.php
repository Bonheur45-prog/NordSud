<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5); // 'fr' | 'en'
            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            // Content blocks stored as JSON: [{type: 'heading', text: ...}, {type: 'text', text: ...}, {type: 'list', items: [...]}, {type: 'image', src: ..., alt: ...}]
            // This matches the heading/paragraph/bullet-list/image pattern seen across every
            // Solutions/Products/Partners page in the old site.
            $table->json('content_blocks')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['page_id', 'locale']); // exactly one FR row + one EN row per page
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_translations');
    }
};
