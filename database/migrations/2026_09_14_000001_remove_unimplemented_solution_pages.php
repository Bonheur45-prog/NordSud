<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * An earlier migration (2026_09_03_000001) tried to remove the two
     * unimplemented Solutions pages by guessing their exact slugs
     * ('consulting-advisory', 'consulting-advisory-services'). If those
     * guesses didn't match the real slugs, that migration would have
     * silently deleted nothing.
     *
     * This one is exclusion-based instead: delete any 'solution'-template
     * page that ISN'T one of the two real, implemented Solutions pages
     * (Solutions Monétiques / Expertise Logiciel — see
     * MigrateLegacyContent's $pageMap, the canonical list). Safe
     * regardless of what the extra pages were actually named.
     */
    public function up(): void
    {
        DB::table('pages')
            ->where('template', 'solution')
            ->whereNotIn('slug', ['solutions-monetiques', 'software-expertise'])
            ->delete();
    }

    public function down(): void
    {
        // The removed pages had no real implemented content to restore.
    }
};
