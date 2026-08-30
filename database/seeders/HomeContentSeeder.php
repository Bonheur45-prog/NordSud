<?php

namespace Database\Seeders;

use App\Models\ClientReference;
use App\Models\Page;
use App\Models\TranslationString;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    /**
     * Fills in real starter content for the home page and the shared
     * Client References list, plus the home.* translation strings the
     * template needs. Idempotent: each content section is only written
     * if it's currently empty, so re-running this after someone has
     * started editing in /admin won't clobber their changes. Text only —
     * no seeder can fabricate real image/video files, so every image
     * field is left blank on purpose. See the accompanying README for
     * exactly what still needs uploading/confirming by hand.
     */
    public function run(): void
    {
        $this->seedHomeContent();
        $this->seedTranslationStrings();
        $this->seedClientReferences();
    }

    protected function seedHomeContent(): void
    {
        $page = Page::where('slug', 'home')->first();

        if (! $page) {
            $this->command?->warn('No page with slug "home" found — skipping home content.');

            return;
        }

        $content = $page->content ?? [];

        if (empty(data_get($content, 'hero.slides'))) {
            $content['hero']['slides'] = [[
                'eyebrow' => ['fr' => null, 'en' => null],
                'headline' => [
                    'fr' => 'Banque Classique et Digitale',
                    'en' => 'Classic and Digital Banking',
                ],
                'subline' => [
                    'fr' => 'Solutions Monétiques et Équipements Bancaires',
                    'en' => 'Payment Solutions and Banking Equipment',
                ],
                'image' => null, // no source file to seed — upload in admin
                'cta_label' => ['fr' => 'Contactez-nous', 'en' => 'Contact us'],
                'cta_link' => '/contact',
            ]];
        }

        if (empty(data_get($content, 'spotlights'))) {
            $content['spotlights'] = [[
                'headline' => [
                    'fr' => "NSI Group s'implante au Kenya",
                    'en' => 'NSI Group establishes a base in Kenya',
                ],
                'body' => [
                    'fr' => "'NSI Group s'implante au Kenya' par le quotidien Daily Nation",
                    'en' => "'NSI Group sets base in Kenya' — Daily Nation",
                ],
                'image' => null, // no source file to seed — upload in admin
                'link' => null, // no confirmed URL yet — see README before publishing
            ]];
        }

        if (empty(data_get($content, 'digital_banking.headline'))) {
            $content['digital_banking']['headline'] = [
                'fr' => 'Canaux de Banque Digitale pour les Petites et Moyennes Institutions Financières',
                'en' => 'Digital Banking Channels for Small and Medium Financial Institutions',
            ];
            $content['digital_banking']['body'] = [
                'fr' => 'NSI et son partenaire Bankingly ont organisé à Kinshasa un petit déjeuner autour de l\'inclusion financière et de la digitalisation ; des solutions innovantes sur la banque en ligne et mobile.',
                'en' => 'NSI and its partner Bankingly hosted a breakfast in Kinshasa on financial inclusion and digitalization, featuring innovative online and mobile banking solutions.',
            ];
            // video/poster/background_image: no source files to seed — upload in admin
        }

        if (empty(data_get($content, 'highlights'))) {
            $content['highlights'] = [
                [
                    'title' => ['fr' => 'Solutions Monétiques', 'en' => 'Payment Solutions'],
                    'description' => [
                        'fr' => 'NSI Group offre des services de création de cartes bancaires, de processing monétique, de solutions de e-payments...',
                        'en' => 'NSI Group offers bank card issuance, card processing, and e-payment solutions...',
                    ],
                    'image' => null,
                    'link' => '/solutions-monetiques',
                ],
                [
                    'title' => ['fr' => 'Cartes Bancaires', 'en' => 'Bank Cards'],
                    'description' => [
                        'fr' => 'NSI Group gère la personnalisation et l\'émission des cartes et des codes confidentiels, l\'émission instantanée de cartes...',
                        'en' => 'NSI Group manages card and PIN personalization and issuance, including instant card issuing...',
                    ],
                    'image' => null,
                    'link' => '/bank-card',
                ],
                [
                    'title' => ['fr' => 'Documents Sécurisés', 'en' => 'Secured Documents'],
                    'description' => [
                        'fr' => 'NSI Group s\'est spécialisée dans la fourniture de tous les documents de valeurs, en utilisant des technologies et des méthodes de travail qui garantissent...',
                        'en' => 'NSI Group specializes in the provision of all value documents, using technologies and working methods that guarantee...',
                    ],
                    'image' => null,
                    'link' => '/secured-documents',
                ],
                [
                    'title' => ['fr' => 'Partenaires & Références', 'en' => 'Partners & References'],
                    'description' => [
                        'fr' => 'Nous avons toute leur confiance et nous travaillons ensemble pour toujours vous assurer un rendu de qualité.',
                        'en' => 'They trust us fully, and we work together to always ensure quality results.',
                    ],
                    'image' => null,
                    'link' => '/references',
                ],
                [
                    'title' => ['fr' => 'NSI Group', 'en' => 'NSI Group'],
                    'description' => [
                        'fr' => 'NSI Group est un groupe panafricain spécialisé dans la production, la personnalisation, de documents sécurisés et dans la fourniture solutions Monétiques.',
                        'en' => 'NSI Group is a pan-African group specializing in the production and personalization of secure documents and the provision of payment solutions.',
                    ],
                    'image' => null,
                    'link' => '/nsi-group',
                ],
                [
                    'title' => ['fr' => 'Centre de Production Kinshasa', 'en' => 'Kinshasa Production Center'],
                    'description' => [
                        'fr' => 'Le centre de personnalisation des moyens de paiement de NSI Group à Kinshasa a été construit en 2014 aux normes strictes de sécurité sur un large terrain...',
                        'en' => 'NSI Group\'s payment personalization center in Kinshasa was built in 2014 to strict security standards on a large site...',
                    ],
                    'image' => null,
                    'link' => '/centre-de-personnalisation-de-kinshasa-2',
                ],
            ];
        }

        if (empty(data_get($content, 'stats_banner.headline'))) {
            $content['stats_banner']['headline'] = [
                'fr' => 'Plus de 60 banques et institutions financières africaines ont fait confiance à NSI Group pour leurs documents de paiement sécurisés et leurs besoins monétiques.',
                'en' => 'More than 60 African banks and financial institutions have trusted NSI Group for their secure payment documents and payment needs.',
            ];
            $content['stats_banner']['cta_label'] = ['fr' => 'Contactez-nous', 'en' => 'Contact us'];
            $content['stats_banner']['cta_link'] = '/contact';
        }

        if (empty(data_get($content, 'contact.headline'))) {
            $content['contact']['headline'] = ['fr' => 'Contactez-nous', 'en' => 'Contact us'];
            $content['contact']['body'] = [
                'fr' => 'Une question ? Un projet ? Notre équipe vous répond dans les meilleurs délais.',
                'en' => 'Have a question or a project in mind? Our team will get back to you promptly.',
            ];
            $content['contact']['link'] = '/contact';
        }

        $page->content = $content;
        $page->save();

        $this->command?->info('Home page content seeded (hero, spotlights, digital banking, highlights, stats banner, contact CTA).');
    }

    protected function seedTranslationStrings(): void
    {
        $strings = [
            'home.highlights_heading' => [
                'group' => 'home',
                'fr' => 'Solutions Monétiques et Équipements Bancaires',
                'en' => 'Payment Solutions and Banking Equipment',
            ],
            'home.highlights_intro' => [
                'group' => 'home',
                'fr' => 'NSI Group est un intégrateur panafricain de solutions monétiques et un fournisseur de documents et supports sécurisés de premier ordre. Nous accompagnons les institutions publiques et établissements financiers dans la mise en place et la sécurisation de leurs documents et moyens de paiements, classiques et électroniques.',
                'en' => 'NSI Group is a pan-African integrator of payment solutions and a leading provider of secure documents and media. We support public institutions and financial establishments in implementing and securing their classic and electronic documents and payment methods.',
            ],
            'home.news_heading' => [
                'group' => 'home',
                'fr' => 'Actualités',
                'en' => 'News',
            ],
            'home.news_badge' => [
                'group' => 'home',
                'fr' => 'Presse',
                'en' => 'Press',
            ],
            'home.read_more' => [
                'group' => 'home',
                'fr' => 'Lire la suite…',
                'en' => 'Read more…',
            ],
            'home.hero_prev' => [
                'group' => 'home',
                'fr' => 'Diapositive précédente',
                'en' => 'Previous slide',
            ],
            'home.hero_next' => [
                'group' => 'home',
                'fr' => 'Diapositive suivante',
                'en' => 'Next slide',
            ],
        ];

        foreach ($strings as $key => $values) {
            TranslationString::updateOrCreate(['key' => $key], $values);
        }

        $this->command?->info('Seeded '.count($strings).' home translation strings.');
    }

    protected function seedClientReferences(): void
    {
        if (ClientReference::query()->exists()) {
            $this->command?->info('Client references already exist — skipping (delete existing rows first if you want to reseed).');

            return;
        }

        // Best-effort read of the logos visible in the old site's screenshots.
        // Several logos were too small/blurry to transcribe with confidence
        // and are deliberately left out rather than guessed — see the
        // README for the full list of what to add by hand, and note that
        // every entry below still needs its actual logo image uploaded via
        // /admin → Client References, since a seeder can't fabricate real
        // image files.
        $names = [
            'FBNBank', 'BPR Atlasmara', 'Access Bank', 'CRDB Bank', 'Bank of Kigali', 'Bank of Africa',
            'Standard Bank', 'Banque de la République du Burundi', 'Banque de Crédit de Bujumbura', 'BCDC',
            'GTBank', 'Amasezerano Community Banking', 'AB Bank Rwanda', 'COPEDU PLC', 'Ecobank', 'Equity Bank',
            'Goshen', 'I&M Bank', 'KCB', 'Letshego', 'China Commercial Bank', 'BRD', 'UFC Umutanguha',
            'Zigama CSS', 'DCB Commercial Bank', 'Bancobu', 'COOPEC', 'Finbank', 'DTB', 'BGF',
            'Banque Centrale du Congo', 'Advans Banque Congo', 'Duterimbere IMF PLC', 'Urwego Bank',
            'Cogebanque', 'CBA', 'Citi',
        ];

        foreach ($names as $i => $name) {
            ClientReference::firstOrCreate(['name' => $name], ['order' => $i]);
        }

        $this->command?->info('Seeded '.count($names).' client reference names (logos still need uploading in admin).');
    }
}
