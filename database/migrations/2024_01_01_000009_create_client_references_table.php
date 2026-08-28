<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A shared global list of client/partner bank logos — used on both the
     * References page (full wall) and the Home page (same logos, same
     * data). Kept as one editable list rather than duplicated entry on
     * both pages, since the real site shows the identical logo set in
     * both places.
     */
    public function up(): void
    {
        Schema::create('client_references', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_references');
    }
};
