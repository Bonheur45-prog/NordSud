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
        $phoneEntries = collect($content['telephones'] ?? [])->keyBy(
            fn (array $entry): string => strtolower(trim((string) data_get($entry, 'country.fr')))
        );

        $content['telephones'] = collect(['rwanda', 'burundi', 'tanzania', 'kenya'])
            ->map(fn (string $country) => $phoneEntries->get($country))
            ->filter()
            ->values()
            ->all();

        $page->update(['content' => $content]);
    }

    public function down(): void
    {
        // Ordering only; no data needs to be restored.
    }
};