<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Corrects the Pages translation model: instead of two fully separate
     * translation rows (French row / English row) that can drift apart in
     * structure, translatable fields now live INLINE as {fr, en} pairs
     * within a single row per page. Shared, non-language fields (icons,
     * image paths) are stored once, not duplicated per language.
     *
     * Simple universal fields (title, meta) get flat fr/en columns.
     * Bespoke per-template structured content (Hero, Values, Stats, etc.)
     * lives in `content`, a single JSON tree where translatable leaves are
     * {"fr": "...", "en": "..."} objects and shared fields (icon, image)
     * are plain values — exactly once, at the item level, not duplicated
     * per language.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('title_fr')->nullable()->after('template');
            $table->string('title_en')->nullable()->after('title_fr');
            $table->string('meta_title_fr')->nullable()->after('title_en');
            $table->string('meta_title_en')->nullable()->after('meta_title_fr');
            $table->string('meta_description_fr', 500)->nullable()->after('meta_title_en');
            $table->string('meta_description_en', 500)->nullable()->after('meta_description_fr');
            $table->json('content')->nullable()->after('meta_description_en');
        });

        Schema::dropIfExists('page_translations');
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'title_fr', 'title_en',
                'meta_title_fr', 'meta_title_en',
                'meta_description_fr', 'meta_description_en',
                'content',
            ]);
        });

        // Note: page_translations is not recreated on rollback —
        // re-run the earlier migration manually if you need to revert fully.
    }
};