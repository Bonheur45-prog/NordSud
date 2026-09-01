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
        Schema::table('press_page_settings', function (Blueprint $table) {
            $table->string('eyebrow_fr')->nullable()->after('hero_background_image');
            $table->string('eyebrow_en')->nullable()->after('eyebrow_fr');
            $table->string('heading_fr')->nullable()->after('eyebrow_en');
            $table->string('heading_en')->nullable()->after('heading_fr');
            $table->string('hero_subtitle_fr')->nullable()->after('heading_en');
            $table->string('hero_subtitle_en')->nullable()->after('hero_subtitle_fr');
            $table->longText('description_fr')->nullable()->after('hero_subtitle_en');
            $table->longText('description_en')->nullable()->after('description_fr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('press_page_settings', function (Blueprint $table) {
            $table->dropColumn(['eyebrow_fr', 'eyebrow_en', 'heading_fr', 'heading_en', 'hero_subtitle_fr', 'hero_subtitle_en', 'description_fr', 'description_en']);
        });
    }
};
