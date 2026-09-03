<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

/**
 * Seeds Solutions Monétiques and Software Expertise with real text from
 * the old site's PDF captures.
 *
 * Usage: php artisan seed:solution-pages
 */
class SeedSolutionPages extends Command
{
    protected $signature = 'seed:solution-pages';

    protected $description = 'Seed real content for Solutions Monétiques and Software Expertise';

    public function handle(): int
    {
        $this->seedSolutionsMonetiques();
        $this->seedSoftwareExpertise();

        return self::SUCCESS;
    }

    protected function seedSolutionsMonetiques(): void
    {
        $page = Page::where('slug', 'solutions-monetiques')->first();

        if (! $page) {
            $this->warn("'solutions-monetiques' page not found — run migrate:legacy-content first.");
            return;
        }

        $content = $page->content ?? [];

        $content['introduction'] = [
            'fr' => "NSI Group a développé une large gamme de solutions bancaires et monétiques couvrant l'ensemble des besoins des institutions financières en matières de paiements électroniques",
            'en' => '',
        ];

        $content['sections'] = [
            [
                'title' => ['fr' => 'Impression et Personnalisation de Cartes', 'en' => ''],
                'bullets' => [
                    ['fr' => 'Personnalisation des cartes bancaires et des codes confidentiels.', 'en' => ''],
                    ['fr' => 'Émission de cartes bancaires (Crédit, Débit ou prépayées).', 'en' => ''],
                    ['fr' => "Émission instantanée de cartes (instant issuing).", 'en' => ''],
                ],
                'image' => $content['sections'][0]['image'] ?? null,
            ],
            [
                'title' => ['fr' => 'Processing Monétique & Gestion Externalisée des activités Monétiques', 'en' => ''],
                'bullets' => [
                    ['fr' => 'Interconnexion avec les partenaires monétiques et routage des transactions (réseaux nationaux et Internationaux).', 'en' => ''],
                    ['fr' => 'Gestion de la compensation.', 'en' => ''],
                    ['fr' => 'Acquisition de transactions à travers différents canaux (GAB, TPE et Internet) et la gestion des commerçants.', 'en' => ''],
                ],
                'image' => $content['sections'][1]['image'] ?? null,
            ],
            [
                'title' => ['fr' => 'E-payments pour Utilities & Opérateurs', 'en' => ''],
                'bullets' => [
                    ['fr' => 'Gestion de la fraude, des réclamations.', 'en' => ''],
                    ['fr' => 'Gestion de la fidélité.', 'en' => ''],
                    ['fr' => 'Gestion de canaux de communication (SMS, Internet, IVR,…)', 'en' => ''],
                    ['fr' => "Édition d'états détaillés et de synthèse de toute l'activité monétique.", 'en' => ''],
                    ['fr' => 'Paiement de factures (Utilities, et opérateur).', 'en' => ''],
                    ['fr' => 'Transfert Western Union.', 'en' => ''],
                ],
                'image' => $content['sections'][2]['image'] ?? null,
            ],
        ];

        $page->update(['title_fr' => 'Solutions Monétiques', 'content' => $content]);
        $this->info('Solutions Monétiques content seeded (3 sections, images preserved if already set).');
    }

    protected function seedSoftwareExpertise(): void
    {
        $page = Page::where('slug', 'software-expertise')->first();

        if (! $page) {
            $this->warn("'software-expertise' page not found — run migrate:legacy-content first.");
            return;
        }

        $content = $page->content ?? [];

        $content['introduction'] = [
            'heading' => [
                'fr' => 'Expertise logicielle de NSI Group pour la gestion des documents sécurisés',
                'en' => '',
            ],
            'text' => [
                'fr' => "<p>NSI Group propose ses solutions logicielles d'assistance intégrée, multi-fonctions:</p>",
                'en' => '',
            ],
        ];

        $content['points'] = [
            ['fr' => "Contrôle complet et automatisé des opérations sur toute la chaine de production.", 'en' => ''],
            ['fr' => 'Modules hautement sécurisés de gestion des commandes, en cours, stocks et livraison ainsi que de la gestion des risques.', 'en' => ''],
            ['fr' => 'Adaptation en conformité avec les normes et les caractéristiques des documents de sécurité définies par les régulateurs dans différents pays.', 'en' => ''],
            ['fr' => "Intégration parfaite des imprimantes, des plates-formes d'encodage numériques et de personnalisation modernes des documents de sécurité.", 'en' => ''],
        ];

        $page->update(['title_fr' => 'Expertise Logiciel', 'content' => $content]);
        $this->info('Software Expertise content seeded (4 capability points, image preserved if already set).');
    }
}
