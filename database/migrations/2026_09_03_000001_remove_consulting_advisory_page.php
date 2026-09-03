<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')->whereIn('slug', [
            'consulting-advisory',
            'consulting-advisory-services',
        ])->delete();
    }

    public function down(): void
    {
        // The removed page had no seeded content to restore.
    }
};