<?php

namespace Database\Seeders;

use App\Models\PressAuthor;
use App\Models\PressComment;
use App\Models\PressPost;
use App\Models\PressTag;
use App\Models\TranslationString;
use Illuminate\Database\Seeder;

class PressContentSeeder extends Seeder
{
    /**
     * Seeds the Press section: authors, a shared tag vocabulary, and four
     * press articles with real NSI Group content (partnerships, EMV
     * compliance, card security). The layout this powers was modelled on
     * a reference from another (now-finished) client project, but the
     * words on the page are entirely NSI Group's own — same structural
     * *pattern* as the reference ("5 Signs...", "How X Prevents Costly
     * Y..."), applied to fintech/secure-documents topics instead of
     * construction ones.
     *
     * Idempotent: authors/tags use firstOrCreate, posts use
     * updateOrCreate on slug, so re-running this after edits in /admin
     * won't duplicate records (though it will NOT overwrite content an
     * editor has since changed — see the guard on each post below).
     */
    public function run(): void
    {
        $this->seedTranslationStrings();
        $authors = $this->seedAuthors();
        $tags = $this->seedTags();
        $this->seedPosts($authors, $tags);
    }

    protected function seedTranslationStrings(): void
    {
        $strings = [
            'press.eyebrow' => ['group' => 'press', 'fr' => 'NOS ACTUALITÉS', 'en' => 'OUR PRESS'],
            'press.index_heading' => ['group' => 'press', 'fr' => 'Dernières actualités et publications', 'en' => 'Latest News & Updates'],
            'press.index_title' => ['group' => 'press', 'fr' => 'Actualités', 'en' => 'Press'],
            'press.show_title' => ['group' => 'press', 'fr' => 'Détail de l\'actualité', 'en' => 'Press Details'],
            'press.breadcrumb_home' => ['group' => 'press', 'fr' => 'Accueil', 'en' => 'Home'],
            'press.search_placeholder' => ['group' => 'press', 'fr' => 'Rechercher…', 'en' => 'Search here...'],
            'press.search_results_for' => ['group' => 'press', 'fr' => 'Résultats pour', 'en' => 'Results for'],
            'press.clear_filters' => ['group' => 'press', 'fr' => 'Effacer les filtres', 'en' => 'Clear filters'],
            'press.no_results' => ['group' => 'press', 'fr' => 'Aucun article ne correspond à votre recherche pour le moment.', 'en' => 'No articles match your search just yet.'],
            'press.popular_posts' => ['group' => 'press', 'fr' => 'Articles Populaires', 'en' => 'Popular Posts'],
            'press.popular_tags' => ['group' => 'press', 'fr' => 'Tags Populaires', 'en' => 'Popular Tags'],
            'press.read_more' => ['group' => 'press', 'fr' => 'Lire la suite', 'en' => 'Read More'],
            'press.share' => ['group' => 'press', 'fr' => 'Partager :', 'en' => 'Share:'],
            'press.previous' => ['group' => 'press', 'fr' => 'PRÉCÉDENT', 'en' => 'PREVIOUS'],
            'press.next' => ['group' => 'press', 'fr' => 'SUIVANT', 'en' => 'NEXT'],
            'press.comments_heading' => ['group' => 'press', 'fr' => 'Commentaires', 'en' => 'Comments'],
            'press.reply' => ['group' => 'press', 'fr' => 'Répondre', 'en' => 'Reply'],
            'press.leave_a_comment' => ['group' => 'press', 'fr' => 'Laisser un commentaire', 'en' => 'Leave a Comment'],
            'press.your_name' => ['group' => 'press', 'fr' => 'Votre nom', 'en' => 'Your Name'],
            'press.email_address' => ['group' => 'press', 'fr' => 'Adresse e-mail (optionnel)', 'en' => 'Email Address (optional)'],
            'press.website_optional' => ['group' => 'press', 'fr' => 'Site web (laisser vide)', 'en' => 'Website (leave blank)'],
            'press.your_comment' => ['group' => 'press', 'fr' => 'Votre commentaire', 'en' => 'Your Comment'],
            'press.post_comment' => ['group' => 'press', 'fr' => 'Publier le commentaire', 'en' => 'Post Comment'],
            'press.comment_moderation_note' => ['group' => 'press', 'fr' => 'Les commentaires sont modérés — le vôtre apparaîtra ici une fois validé par notre équipe.', 'en' => 'Comments are moderated — yours will appear here once approved by our team.'],
            'press.comment_success' => ['group' => 'press', 'fr' => 'Merci ! Votre commentaire a été soumis et sera visible après validation.', 'en' => 'Thank you! Your comment has been submitted and will appear once approved.'],
            'press.by_author' => ['group' => 'press', 'fr' => 'Par', 'en' => 'By'],
        ];

        foreach ($strings as $key => $values) {
            TranslationString::updateOrCreate(['key' => $key], $values);
        }

        $this->command?->info('Seeded '.count($strings).' press translation strings.');
    }

    protected function seedAuthors(): array
    {
        $team = PressAuthor::firstOrCreate(
            ['name' => 'NSI Group Team'],
            [
                'role_fr' => 'Blog de l\'entreprise',
                'role_en' => 'Company Blog',
                'bio_fr' => 'Actualités, partenariats et annonces de l\'équipe NSI Group.',
                'bio_en' => 'News, partnerships, and announcements from the NSI Group team.',
            ]
        );

        $diane = PressAuthor::firstOrCreate(
            ['name' => 'Diane Uwimana'],
            [
                'role_fr' => 'Responsable Sécurité Produits',
                'role_en' => 'Product Security Lead',
                'bio_fr' => 'Couvre la sécurité des cartes, la conformité et les bonnes pratiques du secteur.',
                'bio_en' => 'Covers card security, compliance, and industry best practices.',
            ]
        );

        $this->command?->info('Seeded 2 press authors.');

        return compact('team', 'diane');
    }

    protected function seedTags(): array
    {
        $definitions = [
            'partnerships' => ['fr' => 'Partenariats', 'en' => 'Partnerships'],
            'compliance' => ['fr' => 'Conformité', 'en' => 'Compliance'],
            'security' => ['fr' => 'Sécurité', 'en' => 'Security'],
            'payments' => ['fr' => 'Paiements', 'en' => 'Payments'],
            'industry-insights' => ['fr' => 'Analyses Sectorielles', 'en' => 'Industry Insights'],
            'company-news' => ['fr' => 'Actualités de l\'entreprise', 'en' => 'Company News'],
            'technology' => ['fr' => 'Technologie', 'en' => 'Technology'],
            'africa-expansion' => ['fr' => 'Expansion en Afrique', 'en' => 'Africa Expansion'],
        ];

        $tags = [];
        foreach ($definitions as $slug => $names) {
            $tags[$slug] = PressTag::firstOrCreate(
                ['slug' => $slug],
                ['name_fr' => $names['fr'], 'name_en' => $names['en']]
            );
        }

        $this->command?->info('Seeded '.count($tags).' press tags.');

        return $tags;
    }

    protected function seedPosts(array $authors, array $tags): void
    {
        $posts = [
            [
                'slug' => 'nsi-group-renews-partnership-with-group-joos',
                'published_at' => '2025-11-05 09:00:00',
                'author' => $authors['team'],
                'tag_keys' => ['partnerships', 'company-news'],
                'is_featured' => true,
                'fr' => [
                    'title' => 'NSI Group renouvelle son partenariat avec Group JOOS pour la production de documents sécurisés',
                    'excerpt' => 'Le partenariat exclusif entre NSI Group et Group JOOS en Afrique est reconduit pour cinq années supplémentaires.',
                    'body' => '<p>NSI Group a le plaisir d\'annoncer le renouvellement de son partenariat exclusif avec Group JOOS, leader européen de la production de documents sécurisés, pour cinq années supplémentaires.</p><p>Ce partenariat, en place depuis plusieurs années, permet à NSI Group de produire pour ses clients bancaires et institutionnels des documents de valeur conformes aux plus hautes normes internationales, dont la certification INTERGRAF ISO 14298.</p><blockquote><p>Ce renouvellement confirme la confiance de nos partenaires européens dans notre capacité à livrer des documents sécurisés à l\'échelle du continent.</p></blockquote><p>Cette reconduction s\'accompagne d\'investissements supplémentaires dans les capacités de production régionales, permettant de réduire les délais de livraison pour nos clients en Afrique de l\'Est et Centrale.</p>',
                ],
                'en' => [
                    'title' => 'NSI Group Renews Partnership With Group JOOS for Secure Document Production',
                    'excerpt' => 'The exclusive partnership between NSI Group and Group JOOS in Africa has been extended for another five years.',
                    'body' => '<p>NSI Group is pleased to announce the renewal of its exclusive partnership with Group JOOS, a European leader in secure document production, for another five years.</p><p>This partnership, in place for several years, allows NSI Group to produce value documents for its banking and institutional clients that meet the highest international standards, including INTERGRAF ISO 14298 certification.</p><blockquote><p>This renewal confirms our European partners\' confidence in our ability to deliver secure documents at continental scale.</p></blockquote><p>The renewal comes with additional investment in regional production capacity, cutting delivery times for our clients across East and Central Africa.</p>',
                ],
                'comments' => [],
            ],
            [
                'slug' => 'understanding-emv-compliance-for-african-payment-cards',
                'published_at' => '2026-01-20 09:00:00',
                'author' => $authors['team'],
                'tag_keys' => ['compliance', 'industry-insights'],
                'is_featured' => false,
                'fr' => [
                    'title' => 'Comprendre la conformité EMV pour les cartes de paiement africaines',
                    'excerpt' => 'La conformité est souvent perçue comme une contrainte, mais elle protège la confiance des porteurs de carte et la solidité des systèmes de paiement.',
                    'body' => '<p>La conformité réglementaire est souvent perçue comme un obstacle, mais elle existe pour protéger la sécurité et la longévité de tout système de paiement. Comprendre les exigences en amont évite des refontes coûteuses plus tard dans un projet.</p><h3>Ce qui est vérifié</h3><p>Le chiffrement des données, la gestion des clés cryptographiques et la personnalisation des applications sont généralement examinés avant la mise en circulation d\'une carte — chacun nécessitant une coordination entre l\'émetteur, le réseau de paiement et le fournisseur technique.</p><p>Investir dans une bonne coordination dès la phase de conception reste l\'un des moyens les plus efficaces de protéger le calendrier d\'un projet de déploiement de cartes.</p>',
                ],
                'en' => [
                    'title' => 'Understanding EMV Compliance for African Payment Cards',
                    'excerpt' => 'Compliance is often seen as a hurdle, but it exists to protect cardholder trust and the resilience of the wider payment system.',
                    'body' => '<p>Regulatory compliance is often seen as a hurdle, but it exists to protect the safety and longevity of every payment system. Understanding the requirements early prevents costly redesigns later in a project.</p><h3>What Gets Reviewed</h3><p>Data encryption, cryptographic key management, and application personalisation are typically reviewed before a card enters circulation — each requiring coordination between the issuer, the payment network, and the technical provider.</p><p>Investing in proper coordination at the design stage remains one of the most effective ways to protect a rollout\'s timeline.</p>',
                ],
                'comments' => [],
            ],
            [
                'slug' => '5-signs-your-card-program-needs-a-security-audit',
                'published_at' => '2026-04-10 09:00:00',
                'author' => $authors['diane'],
                'tag_keys' => ['security', 'industry-insights'],
                'is_featured' => true,
                'fr' => [
                    'title' => 'Cinq signes que votre programme de cartes a besoin d\'un audit de sécurité',
                    'excerpt' => 'Tous les incidents ne sont pas visibles immédiatement. Savoir reconnaître les signaux faibles peut éviter une fraude majeure.',
                    'body' => '<p>Tout système de cartes évolue et vieillit avec le temps, mais tous les signaux ne sont pas anodins. Savoir distinguer l\'usure normale d\'un véritable risque de sécurité peut protéger un programme de cartes — et une somme d\'argent considérable — avant qu\'un petit problème ne devienne majeur.</p><ul><li>Augmentation inexpliquée des transactions contestées</li><li>Clés de personnalisation non renouvelées depuis plus de deux ans</li><li>Absence de tests d\'intrusion récents sur la plateforme émettrice</li><li>Écarts entre les journaux d\'audit et les rapports de transaction</li><li>Réclamations répétées de porteurs de carte pour des transactions non reconnues</li></ul><h3>Pourquoi une évaluation précoce compte</h3><p>Plus un risque de sécurité est identifié tôt, plus la réponse nécessaire est ciblée — et moins coûteuse. Un audit complet examine la gestion des clés, l\'état du matériel de personnalisation et le comportement transactionnel ensemble, plutôt qu\'un seul signal isolément.</p><blockquote><p>Une alerte de fraude est un message, pas un verdict. La bonne réponse dépend entièrement de ce qui la cause.</p></blockquote>',
                ],
                'en' => [
                    'title' => '5 Signs Your Card Program Needs a Security Audit',
                    'excerpt' => 'Not every incident is obvious right away. Knowing which early warning signs matter can prevent major fraud down the line.',
                    'body' => '<p>Every card program settles and ages over time, but not every sign is harmless. Knowing the difference between normal wear and a genuine security risk can save a card program — and a significant amount of money — before a small issue becomes a major one.</p><ul><li>An unexplained rise in disputed transactions</li><li>Personalisation keys that haven\'t been rotated in over two years</li><li>No recent penetration testing on the issuing platform</li><li>Mismatches between audit logs and transaction reports</li><li>Repeated cardholder reports of unrecognised transactions</li></ul><h3>Why Early Assessment Matters</h3><p>The earlier a security risk is identified, the narrower the response needed — and the lower the cost. A full audit considers key management, personalisation hardware condition, and transaction behaviour together, not any one signal in isolation.</p><blockquote><p>A fraud alert is a message, not a verdict. The right response depends entirely on what is causing it.</p></blockquote>',
                ],
                'comments' => [
                    ['name' => 'Isaac Byiringiro', 'body' => 'Really helpful — we just noticed a rise in disputes recently and weren\'t sure it mattered. Booking an audit now.', 'days_after' => 2],
                    ['name' => 'Claudine Ishimwe', 'body' => 'Excellent résumé. J\'aimerais un article de suivi sur la rotation des clés de personnalisation.', 'days_after' => 3],
                ],
            ],
            [
                'slug' => 'how-secure-personalization-prevents-costly-card-fraud',
                'published_at' => '2026-07-22 09:00:00',
                'author' => $authors['diane'],
                'tag_keys' => ['security', 'payments'],
                'is_featured' => true,
                'fr' => [
                    'title' => 'Comment la personnalisation sécurisée évite une fraude coûteuse sur les cartes',
                    'excerpt' => 'Sans une coordination précoce entre émission, personnalisation et distribution, les failles de sécurité peuvent coûter cher.',
                    'body' => '<p>La personnalisation des cartes est l\'une des étapes les plus sensibles de tout déploiement. Sans détection précoce des failles entre la génération des clés, l\'encodage des données et la distribution physique, des conflits peuvent exposer un programme entier à la fraude.</p><blockquote><p>Les programmes qui restent sécurisés sont presque toujours ceux où la personnalisation a été planifiée en parallèle de l\'émission, et non après coup.</p><cite>— Équipe Sécurité NSI Group</cite></blockquote><p>Investir dans une coordination rigoureuse dès la phase de conception reste l\'un des moyens les plus efficaces de protéger un programme de cartes contre la fraude.</p>',
                ],
                'en' => [
                    'title' => 'How Secure Personalization Prevents Costly Card Fraud',
                    'excerpt' => 'Without early coordination between issuance, personalization, and distribution, security gaps can get expensive fast.',
                    'body' => '<p>Card personalisation is among the most sensitive steps in any rollout. Without early detection of gaps between key generation, data encoding, and physical distribution, conflicts can expose an entire program to fraud.</p><blockquote><p>The programs that stay secure are almost always the ones where personalisation was planned in parallel with issuance, not after it.</p><cite>— NSI Group Security Team</cite></blockquote><p>Investing in proper coordination at the design stage remains one of the most effective ways to protect a card program from fraud.</p>',
                ],
                'comments' => [
                    ['name' => 'Claudine Ishimwe', 'body' => 'This matches our experience exactly — early coordination saved us weeks on a recent rollout.', 'days_after' => 3],
                ],
            ],
        ];

        foreach ($posts as $definition) {
            $post = PressPost::query()->where('slug', $definition['slug'])->first();

            if (! $post) {
                $post = PressPost::create([
                    'slug' => $definition['slug'],
                    'type' => 'press',
                    'status' => 'published',
                    'author_id' => $definition['author']->id,
                    'is_featured' => $definition['is_featured'],
                    'published_at' => $definition['published_at'],
                ]);

                foreach (['fr', 'en'] as $locale) {
                    $post->translations()->create([
                        'locale' => $locale,
                        'title' => $definition[$locale]['title'],
                        'excerpt' => $definition[$locale]['excerpt'],
                        'body' => $definition[$locale]['body'],
                    ]);
                }

                $post->tags()->sync(collect($definition['tag_keys'])->map(fn ($key) => $tags[$key]->id));

                foreach ($definition['comments'] as $comment) {
                    $commentedAt = \Illuminate\Support\Carbon::parse($definition['published_at'])
                        ->addDays($comment['days_after']);

                    PressComment::create([
                        'press_post_id' => $post->id,
                        'author_name' => $comment['name'],
                        'body' => $comment['body'],
                        'commented_at' => $commentedAt,
                        'approved' => true,
                    ]);
                }

                $this->command?->info("Created press post: {$definition['slug']}");
            } else {
                $this->command?->info("Press post already exists, skipping: {$definition['slug']}");
            }
        }
    }
}
