<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fintech/domain terminology reference so "cartes bancaires" is always
     * "bank cards" everywhere, never "payment cards" in one place and
     * "bank cards" in another. Shown as a reference panel in the admin
     * when editing page/translation content.
     */
    public function up(): void
    {
        Schema::create('glossary_terms', function (Blueprint $table) {
            $table->id();
            $table->string('term_fr');
            $table->string('term_en');
            $table->text('notes')->nullable(); // usage context for whoever is translating
            $table->timestamps();

            $table->index('term_fr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glossary_terms');
    }
};
