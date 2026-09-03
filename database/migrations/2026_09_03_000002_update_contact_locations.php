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
        $content['addresses'] = collect($content['addresses'] ?? [])
            ->reject(fn (array $entry): bool => str_contains(
                strtolower((string) data_get($entry, 'country.fr')),
                'congo'
            ))
            ->values()
            ->all();

        $telephones = collect($content['telephones'] ?? [])
            ->reject(fn (array $entry): bool => str_contains(
                strtolower((string) data_get($entry, 'country.fr')),
                'congo'
            ))
            ->values();

        $kenya = [
            'country' => ['fr' => 'Kenya', 'en' => 'Kenya'],
            'details' => ['fr' => '+254 768 068326 - Tatu City, Nairobi, Kenya', 'en' => '+254 768 068326 - Tatu City, Nairobi, Kenya'],
        ];

        $telephones = $telephones
            ->reject(fn (array $entry): bool => str_contains(
                strtolower((string) data_get($entry, 'country.fr')),
                'kenya'
            ))
            ->push($kenya);

        $content['telephones'] = $telephones->values()->all();
        $page->update(['content' => $content]);
    }

    public function down(): void
    {
        // The removed Congo contact data is intentionally not restored.
    }
};