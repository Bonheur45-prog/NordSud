<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per logical page — NOT per language.
     * This is the fix for the old site's Polylang setup, where
     * "nsi-groupe" (FR) and "nsi-group" (EN) were two disconnected
     * pages that could drift apart independently.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // shared across languages, e.g. "nsi-group"
            $table->string('template'); // home, about, solution, product, partner, contact, legal
            $table->string('status')->default('draft'); // draft | published
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
