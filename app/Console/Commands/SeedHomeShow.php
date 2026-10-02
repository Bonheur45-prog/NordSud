<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

/**
 * Seeds the starter scenes of the home page "Show" (the cinematic slideshow
 * that replaces the video player in the Digital Banking Feature section).
 *
 * Scenes are seeded WITHOUT photos on purpose: upload licensed or client-owned
 * images per scene in the admin (Home Page > Video Section Show). Until then
 * each scene uses its drawn background. Existing scenes are never overwritten
 * unless --force is given; other home page content is left untouched.
 *
 * Usage: php artisan seed:home-show [--force]
 */
class SeedHomeShow extends Command
{
    protected $signature = 'seed:home-show {--force : Replace scenes that already exist}';

    protected $description = 'Seed the starter scenes for the home page video-section show';

    public function handle(): int
    {
        $page = Page::where('slug', 'home')->first();

        if (! $page) {
            $this->warn("'home' page not found — run the home page seeder / migrate:legacy-content first.");

            return self::FAILURE;
        }

        $content = $page->content ?? [];
        $existing = data_get($content, 'digital_banking.show_scenes', []);

        if (! empty($existing) && ! $this->option('force')) {
            $this->info('Scenes already exist — nothing changed (use --force to replace them).');

            return self::SUCCESS;
        }

        $content['digital_banking']['show_scenes'] = $this->scenes();
        $content['digital_banking']['show_size'] = $content['digital_banking']['show_size'] ?? 'large';
        $content['digital_banking']['show_music'] = $content['digital_banking']['show_music'] ?? true;

        $page->update(['content' => $content]);

        $this->info('Seeded '.count($content['digital_banking']['show_scenes']).' show scenes.');

        return self::SUCCESS;
    }

    /** @return array<int, array<string, mixed>> */
    protected function scenes(): array
    {
        $scene = fn (array $fields) => array_merge([
            'background' => 'net', 'focal' => 'center', 'duration' => 6.5,
            'layout' => 'full', 'align' => 'l', 'motion' => 'zi', 'transition' => 'fade',
            'text_animation' => 'mask', 'subline_style' => 'fade',
        ], $fields);

        return [
            $scene([
                'chapter' => ['fr' => 'Origine', 'en' => 'Origin'],
                'headline' => ['fr' => "Une histoire [africaine], née en Afrique de l'Est", 'en' => 'An [African] story, born in East Africa'],
                'subline' => ['fr' => "L'Afrique mérite des solutions à la hauteur de ses ambitions.", 'en' => 'Africa deserves solutions that match its ambitions.'],
                'duration' => 7,
            ]),
            $scene([
                'chapter' => ['fr' => 'Mission', 'en' => 'Mission'],
                'headline' => ['fr' => 'Au service des [Africains], au quotidien', 'en' => 'Serving [Africans], every day'],
                'subline' => ['fr' => 'Intégrateur panafricain de solutions monétiques.', 'en' => 'Pan-African integrator of payment solutions.'],
                'layout' => 'split', 'motion' => 'pl', 'transition' => 'focus',
                'text_animation' => 'words', 'subline_style' => 'type',
            ]),
            $scene([
                'chapter' => ['fr' => 'Banque digitale', 'en' => 'Digital banking'],
                'headline' => ['fr' => 'La banque [digitale], simple et sécurisée', 'en' => '[Digital] banking, simple and secure'],
                'subline' => ['fr' => 'Des services financiers plus proches de chacun.', 'en' => 'Financial services closer to everyone.'],
                'motion' => 'zo', 'transition' => 'zoom', 'text_animation' => 'slide',
            ]),
            $scene([
                'chapter' => ['fr' => 'Cartes', 'en' => 'Cards'],
                'headline' => ['fr' => 'Des cartes [bancaires] fiables', 'en' => 'Reliable [bank] cards'],
                'subline' => ['fr' => 'Conçues pour nos banques partenaires.', 'en' => 'Made for our partner banks.'],
                'motion' => 'st', 'transition' => 'wipe',
            ]),
            $scene([
                'chapter' => ['fr' => 'Documents', 'en' => 'Documents'],
                'headline' => ['fr' => 'Des documents [sécurisés], pour chaque identité', 'en' => '[Secure] documents, for every identity'],
                'subline' => ['fr' => 'Permis de conduire, actes de naissance, état civil.', 'en' => 'Driving licences, birth certificates, civil records.'],
                'background' => 'doc', 'motion' => 'pr', 'transition' => 'focus',
                'text_animation' => 'fade', 'subline_style' => 'type',
            ]),
            $scene([
                'chapter' => ['fr' => 'Logiciels', 'en' => 'Software'],
                'headline' => ['fr' => 'Une expertise [logicielle] locale', 'en' => 'Local [software] expertise'],
                'subline' => ['fr' => 'Des systèmes adaptés aux réalités africaines.', 'en' => 'Systems built for African realities.'],
                'background' => 'code', 'align' => 'r', 'transition' => 'zoom', 'text_animation' => 'words',
            ]),
            $scene([
                // Upload the FinPay Africa logo in the admin ("Logo badge") — it cannot be seeded.
                'chapter' => ['fr' => 'Filiale', 'en' => 'Subsidiary'],
                'headline' => ['fr' => "Plus de 20 ans d'expérience, puis [FinPay Africa]", 'en' => 'Over 20 years of experience, then [FinPay Africa]'],
                'subline' => ['fr' => "Notre filiale, créée pour connecter l'Afrique.", 'en' => 'Our subsidiary, created to connect Africa.'],
                'motion' => 'zo', 'transition' => 'focus', 'text_animation' => 'words', 'subline_style' => 'type', 'duration' => 7.5,
            ]),
            $scene([
                'chapter' => ['fr' => 'Ensemble', 'en' => 'Together'],
                'headline' => ['fr' => 'NSI Group, partout en [Afrique]', 'en' => 'NSI Group, everywhere in [Africa]'],
                'subline' => ['fr' => 'Parlons de votre projet.', 'en' => "Let's talk about your project."],
                'align' => 'c', 'motion' => 'zo', 'transition' => 'wipe',
                'cta_label' => ['fr' => 'Nous contacter', 'en' => 'Contact us'], 'cta_link' => '/contact',
            ]),
        ];
    }
}
