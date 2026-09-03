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
        $addresses = collect($content['addresses'] ?? [])
            ->reject(fn (array $entry): bool => str_contains(
                strtolower((string) data_get($entry, 'country.fr')),
                'congo'
            ));

        foreach ([
            [
                'country' => ['fr' => 'Tanzania', 'en' => 'Tanzania'],
                'address' => ['fr' => 'Tanzania', 'en' => 'Tanzania'],
            ],
            [
                'country' => ['fr' => 'Kenya', 'en' => 'Kenya'],
                'address' => ['fr' => 'Tatu City, Nairobi, Kenya', 'en' => 'Tatu City, Nairobi, Kenya'],
            ],
        ] as $countryAddress) {
            $country = strtolower($countryAddress['country']['fr']);
            $addresses = $addresses
                ->reject(fn (array $entry): bool => strtolower((string) data_get($entry, 'country.fr')) === $country)
                ->push($countryAddress);
        }

        $content['addresses'] = $addresses->values()->all();
        $content['telephones'] = collect($content['telephones'] ?? [])
            ->reject(fn (array $entry): bool => str_contains(
                (string) data_get($entry, 'details.fr'),
                '+243'
            ))
            ->reject(fn (array $entry): bool => strtolower((string) data_get($entry, 'country.fr')) === 'tanzania')
            ->push([
                'country' => ['fr' => 'Tanzania', 'en' => 'Tanzania'],
                'details' => ['fr' => '+255 713 689 686', 'en' => '+255 713 689 686'],
            ])
            ->values()
            ->all();

        $page->update(['content' => $content]);
    }

    public function down(): void
    {
        // Contact data is intentionally not reverted to the removed Congo entry.
    }
};