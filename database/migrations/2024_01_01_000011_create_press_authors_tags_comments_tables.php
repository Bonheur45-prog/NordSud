<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('press_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // proper noun — not translated
            $table->string('role_fr')->nullable();
            $table->string('role_en')->nullable();
            $table->text('bio_fr')->nullable();
            $table->text('bio_en')->nullable();
            $table->string('avatar')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('x_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->timestamps();
        });

        Schema::create('press_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('press_post_press_tag', function (Blueprint $table) {
            $table->foreignId('press_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('press_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['press_post_id', 'press_tag_id']);
        });

        Schema::create('press_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('press_post_id')->constrained()->cascadeOnDelete();
            $table->string('author_name');
            $table->string('author_email')->nullable();
            $table->text('body');
            $table->timestamp('commented_at')->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();

            $table->index(['press_post_id', 'approved']);
        });

        Schema::table('press_posts', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('type')
                ->constrained('press_authors')->nullOnDelete();
            $table->boolean('is_featured')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('press_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('author_id');
            $table->dropColumn('is_featured');
        });

        Schema::dropIfExists('press_comments');
        Schema::dropIfExists('press_post_press_tag');
        Schema::dropIfExists('press_tags');
        Schema::dropIfExists('press_authors');
    }
};
