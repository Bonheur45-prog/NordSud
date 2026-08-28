<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

/**
 * KONA I and VERVE partner pages do not exist in the legacy WordPress DB
 * backup at all (verified: no matching post_title/post_name in ns_posts,
 * any status) — the backup predates these two partnerships being added to
 * the live site. Their content here is transcribed directly from the
 * GoFullPage PDF captures provided at the start of this project, not
 * guessed or reconstructed from ambiguous data.
 *
 * English translations are intentionally left blank (flagged missing,
 * same as everywhere else) rather than pieced together from unreliable
 * fragments found in leftover WordPress revision data.
 *
 * Usage: php artisan seed:missing-partners
 */
class SeedMissingPartners extends Command
{
    protected $signature = 'seed:missing-partners';

    protected $description = 'Create KONA I and VERVE partner pages (absent from the legacy DB) and verify existing partner/product templates';

    public function handle(): int
    {
        $this->info('Creating KONA I...');
        Page::updateOrCreate(
            ['slug' => 'kona-i'],
            [
                'template' => 'partner',
                'status' => 'published',
                'title_fr' => 'KONA I',
                'title_en' => null,
                'content' => [
                    'paragraphs' => [
                        [
                            'fr' => "Kona I est une société technologique qui fournit des solutions et des plates-formes complètes au marché mondial des Fin-tech. Depuis la création de la société en 1998, Kona I a développé et commercialisé le premier système intégré de transport par carte bus-métro en Corée.",
                            'en' => '',
                        ],
                        [
                            'fr' => "Depuis 2004, Kona I fournit les puces IC à l'industrie des télécommunications, à l'industrie bancaire et aux gouvernements à l'aide de son système exclusif d'exploitation de puce KONA.",
                            'en' => '',
                        ],
                        [
                            'fr' => "NSI Group utilise les cartes Kona I qui sont entièrement conformes à la norme EMV. Les cartes Kona I basées sur la technologie Java Card permettent à NSI Group de développer et de fournir rapidement des applications de cartes à puce pour répondre aux demandes des clients.",
                            'en' => '',
                        ],
                    ],
                ],
            ]
        );

        $this->info('Creating VERVE...');
        Page::updateOrCreate(
            ['slug' => 'verve'],
            [
                'template' => 'partner',
                'status' => 'published',
                'title_fr' => 'VERVE',
                'title_en' => null,
                'content' => [
                    'paragraphs' => [
                        [
                            'fr' => "Verve International est une multinationale Nigérienne de technologie financière et une marque Pan-Africaine de cartes de paiement appartenant à Interswitch Group.",
                            'en' => '',
                        ],
                        [
                            'fr' => "Le partenariat entre NSI Group et Interswitch Group se fonde sur une vision partagée d'une «croissance africaine portée par les entreprises africaines» et une complémentarité territoriale qui relie l'Afrique anglophone à l'Afrique francophone.",
                            'en' => '',
                        ],
                        [
                            'fr' => "Verve est la carte de paiement la plus utilisée au Nigeria, représentant 18 millions des 25 millions de cartes en circulation dans le pays. Verve a également été lancé au Kenya et sera bientôt lancé dans d'autres pays de l'Afrique de l'Est.",
                            'en' => '',
                        ],
                    ],
                ],
            ]
        );

        $this->newLine();
        $this->info('Verifying existing partner/product pages have correct templates...');

        foreach (['group-joos' => 'partner', 'bank-card' => 'product', 'secured-documents' => 'product'] as $slug => $expectedTemplate) {
            $page = Page::where('slug', $slug)->first();

            if (! $page) {
                $this->warn("  '{$slug}' not found — run migrate:legacy-content first.");

                continue;
            }

            if ($page->template !== $expectedTemplate) {
                $page->update(['template' => $expectedTemplate]);
                $this->line("  '{$slug}' template corrected to '{$expectedTemplate}'.");
            } else {
                $this->line("  '{$slug}' already template='{$expectedTemplate}'. OK.");
            }
        }

        $this->newLine();
        $this->info('Done. Partners group should now show: Group JOOS, KONA I, VERVE. Products group: Bank Cards, Secured Documents.');

        return self::SUCCESS;
    }
}
