<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Flags rows imported from the legacy TranslatePress dictionary where
     * the 'fr' (canonical) value looks like it's actually English — a real
     * data quality issue found in the old dump, where TRP sometimes stored
     * English-page text as if it were the French source. These need a human
     * to look at them; no automated heuristic is reliable enough to fix
     * them silently.
     */
    public function up(): void
    {
        Schema::table('translation_strings', function (Blueprint $table) {
            $table->boolean('suspect_source_language')->default(false)->after('en');
        });
    }

    public function down(): void
    {
        Schema::table('translation_strings', function (Blueprint $table) {
            $table->dropColumn('suspect_source_language');
        });
    }
};