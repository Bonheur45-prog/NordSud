<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = Page::where('slug', 'contact')->first();

        if (! $page) {
            return;
        }

        $content = $page->content ?? [];
        $content['telephones'] = collect($content['telephones'] ?? [])
            ->reject(fn (array $entry): bool => str_contains(
                (string) data_get($entry, 'details.fr'),
                '+243'
            ))
            ->values()
            ->all();

        $page->update(['content' => $content]);
    }

    public function down(): void
    {
        // The stale Congo telephone entry is intentionally not restored.
    }
};