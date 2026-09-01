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
        Schema::create('press_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_background_image')->nullable();
            $table->timestamps();
        });
        
        // Seed with one default row
        DB::table('press_page_settings')->insert(['created_at' => now(), 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('press_page_settings');
    }
};
