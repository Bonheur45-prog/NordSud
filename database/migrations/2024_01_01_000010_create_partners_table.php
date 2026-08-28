<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Partners (Group JOOS, Kona I, Verve, and any future ones) are a real
     * growable collection — unlike About/Solutions/Products, which are a
     * fixed set of singleton pages. This is why Partners is a genuine
     * Filament Resource (like PressPosts) rather than a fixed singleton
     * editor: editors need a real "+ Add Partner" action.
     *
     * `content` is intentionally flexible JSON — Group JOOS needs
     * certifications/production-sites/stats blocks, Kona I and Verve need
     * none of that, just a logo and a couple of paragraphs. Optional
     * blocks simply stay empty for simple partners rather than forcing
     * every partner into JOOS's complexity.
     */
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('name_fr');
            $table->string('name_en')->nullable();
            $table->json('content')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->string('status')->default('published'); // published|draft
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
