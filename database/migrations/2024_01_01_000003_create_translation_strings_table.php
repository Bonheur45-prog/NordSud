<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replaces the old TranslatePress dictionary table
     * (ns_trp_dictionary_fr_fr_en_us), where ~300 of ~550 strings
     * were left untranslated with no visibility for editors.
     *
     * `fr` is NOT NULL (canonical source).
     * `en` IS nullable — so the admin UI can flag it in red rather
     * than silently falling back to blank/French text like before.
     */
    public function up(): void
    {
        Schema::create('translation_strings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g. "nav.solutions", "cta.contact_us", "footer.rights_reserved"
            $table->string('group')->default('global'); // nav | footer | forms | global | press | contact
            $table->text('fr'); // canonical, required
            $table->text('en')->nullable(); // flagged as missing in admin when null
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_strings');
    }
};
