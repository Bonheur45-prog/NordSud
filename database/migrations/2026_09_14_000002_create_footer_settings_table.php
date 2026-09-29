<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('x_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->timestamps();
        });

        // Seed one row with the URLs that were hardcoded in footer.blade.php,
        // so switching to CMS control doesn't change anything visually —
        // the admin can edit them from here on.
        DB::table('footer_settings')->insert([
            'facebook_url' => 'https://www.facebook.com/nsigroup',
            'instagram_url' => 'https://www.instagram.com/nsigroup/',
            'x_url' => 'https://twitter.com/nsigroup',
            'linkedin_url' => 'https://www.linkedin.com/company/nsi-group/',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
