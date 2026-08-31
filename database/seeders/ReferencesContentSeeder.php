<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class ReferencesContentSeeder extends Seeder
{
    /**
     * Text-only seed for the References page intro paragraph, transcribed
     * from the old site's screenshot. Idempotent — only writes if the
     * introduction field is currently empty. The hero image still needs
     * uploading by hand in /admin; the logo wall itself needs no seeding
     * here since it's the shared ClientReference list already seeded by
     * HomeContentSeeder.
     */
    public function run(): void
    {
        $page = Page::where('slug', 'references')->first();

        if (! $page) {
            $this->command?->warn('No page with slug "references" found — skipping.');

            return;
        }

        $content = $page->content ?? [];

        if (empty(data_get($content, 'introduction.fr'))) {
            $content['introduction'] = [
                'fr' => 'Nous avons toute leur confiance et nous travaillons ensemble pour toujours vous assurer un rendu de qualité.',
                'en' => 'They place their full trust in us, and we work together to always ensure quality results.',
            ];
        }

        $page->content = $content;
        $page->save();

        $this->command?->info('References page introduction seeded. Hero image still needs uploading in admin.');
    }
}
