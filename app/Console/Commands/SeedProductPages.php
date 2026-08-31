<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

/**
 * Seeds Bank Card and Secured Documents with the real text from the old
 * site's PDF captures. French only — English left blank/missing, same
 * convention as everywhere else migrated from the legacy site.
 *
 * Uses an explicit per-key merge (not array_merge at the top level) so
 * any image already uploaded via admin is never silently wiped — same
 * precaution as SeedAboutPages.
 *
 * Usage: php artisan seed:product-pages
 */
class SeedProductPages extends Command
{
    protected $signature = 'seed:product-pages';

    protected $description = 'Seed real content for Bank Card and Secured Documents';

    public function handle(): int
    {
        $this->seedBankCard();
        $this->seedSecuredDocuments();

        return self::SUCCESS;
    }

    protected function seedBankCard(): void
    {
        $page = Page::where('slug', 'bank-card')->first();

        if (! $page) {
            $this->warn("'bank-card' page not found — run migrate:legacy-content first.");
            return;
        }

        $content = $page->content ?? [];

        $content['introduction'] = [
            'fr' => 'NSI Group assure la personnalisation et la fourniture des cartes bancaires',
            'en' => '',
        ];

        $content['sections'] = [
            [
                'title' => ['fr' => 'NSI Group offre une prestation complète:', 'en' => ''],
                'bullets' => [
                    ['fr' => 'Gestion des clés cryptographiques.', 'en' => ''],
                    ['fr' => 'Gestion des supports carte.', 'en' => ''],
                    ['fr' => 'Aide à la conception.', 'en' => ''],
                    ['fr' => 'Impression des cartes.', 'en' => ''],
                    ['fr' => 'Personnalisation des cartes (graphique, magnétique et puce électronique).', 'en' => ''],
                    ['fr' => 'Par embossage ou en infill.', 'en' => ''],
                    ['fr' => 'Personnalisation des cartes à puce (Data préparation).', 'en' => ''],
                    ['fr' => 'Encodage de la piste magnétique en utilisant les normes standard.', 'en' => ''],
                    ['fr' => "Possibilité d'imprimer en utilisant une encre à gratter.", 'en' => ''],
                    ['fr' => 'Calcul des codes confidentiels (PIN) en respectant les normes internationales de sécurité et de confidentialité (gestion sécurisée des clés et du Hardware Security Module HSM).', 'en' => ''],
                    ['fr' => 'Impression, mise sous pli et expédition suivant les besoins du client en termes de conditionnement et de lieu de livraison.', 'en' => ''],
                ],
                'image' => $content['sections'][0]['image'] ?? null,
            ],
            [
                'title' => ['fr' => 'Une activité sécurisée et alignées sur les normes internationales', 'en' => ''],
                'description' => [
                    'fr' => '<p>Ces activités sont réalisées au sein des sites NSI hautement sécurisés, et répondant aux normes PCI-DSS:</p>',
                    'en' => '',
                ],
                'bullets' => [
                    ['fr' => "Contrôle d'accès aux aires de production par carte à mémoire.", 'en' => ''],
                    ['fr' => 'Surveillance vidéo couleur avec enregistrement 24 par 7.', 'en' => ''],
                    ['fr' => "Détecteurs d'intrusion et barrières infrarouges.", 'en' => ''],
                    ['fr' => 'Système d\'enregistrement des événements.', 'en' => ''],
                    ['fr' => 'Système de télésurveillance à distance effectuée par une société spécialisée.', 'en' => ''],
                ],
                'image' => $content['sections'][1]['image'] ?? null,
            ],
            [
                'title' => ['fr' => 'Une organisation et des processus éprouvés', 'en' => ''],
                'bullets' => [
                    ['fr' => "L'application de processus et de procédures éprouvées permet d'assurer un haut niveau de sécurité et de confidentialité.", 'en' => ''],
                    ['fr' => "Une politique de formation et de sensibilisation continue pour l'ensemble du personnel de notre site assurent le respect opérationnel des procédures.", 'en' => ''],
                ],
                'image' => $content['sections'][2]['image'] ?? null,
            ],
        ];

        $page->update(['title_fr' => 'Carte Bancaire', 'content' => $content]);
        $this->info('Bank Card content seeded (3 sections, images preserved if already set).');
    }

    protected function seedSecuredDocuments(): void
    {
        $page = Page::where('slug', 'secured-documents')->first();

        if (! $page) {
            $this->warn("'secured-documents' page not found — run migrate:legacy-content first.");
            return;
        }

        $content = $page->content ?? [];

        $content['introduction'] = [
            'fr' => "NSI Group est l'expert dans la création et la fourniture de tous les documents de valeurs, en utilisant des technologies et des méthodes de travail qui garantissent l'authentification, l'inviolabilité et la traçabilité des produits",
            'en' => '',
        ];

        $content['security'] = array_merge($content['security'] ?? [], [
            'description' => [
                'fr' => '<p>Ce résultat est obtenu grâce à l\'utilisation conjuguée de plusieurs mécanismes dont les principaux sont :</p>',
                'en' => '',
            ],
            'bullets' => [
                ['fr' => 'Du papier vierge à l\'imprimé sécurisé difficile à contrefaire et à falsifier, selon la norme NF-K-11-112', 'en' => ''],
                ['fr' => "Des papiers d'impression dotés de plusieurs éléments de sécurité,", 'en' => ''],
                ['fr' => 'Des encres spéciales, fluorescentes ou à variation de teinte,', 'en' => ''],
                ['fr' => "Des types spécifiques d'impression de sécurité,", 'en' => ''],
                ['fr' => "La numérotation des documents à l'encre fluorescente sous la Lumière UV ou à l'encre migrante.", 'en' => ''],
                ['fr' => 'Impression dynamique de la ligne CMC7', 'en' => ''],
            ],
        ]);

        $content['services'] = array_merge($content['services'] ?? [], [
            'description' => [
                'fr' => '<p>NSI propose une offre intégrée de services depuis la conception des documents, la détermination des niveaux de sécurité requis, le stockage, la personnalisation, la livraison des documents et le reporting régulier sur la consommation effective.</p>',
                'en' => '',
            ],
            'bullets' => [
                ['fr' => 'Impression et personnalisation de documents bancaires (chèques, ordres de virement, lettres de change…).', 'en' => ''],
                ['fr' => "Attestations, certificats et documents administratifs (actes d'État Civil, notes de perception, tickets de stationnement, titres de propriété, documents d'Identification…).", 'en' => ''],
                ['fr' => 'Autres documents de valeur (reçus, vignettes automobiles, carte de contrôle technique…).', 'en' => ''],
            ],
        ]);

        $content['closing'] = [
            'fr' => '<p>La fourniture de documents sécurisés peut être réalisée en mode « pay per use », ce qui permet au client de rendre son coût d\'approvisionnement variable.</p>',
            'en' => '',
        ];

        $page->update(['title_fr' => 'Documents Sécurisés', 'content' => $content]);
        $this->info('Secured Documents content seeded (2 bands + closing, images preserved if already set).');
    }
}
