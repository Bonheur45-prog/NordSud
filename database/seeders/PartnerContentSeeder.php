<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PartnerContentSeeder extends Seeder
{
    /**
     * Fills in real starter content (text only — see HomeContentSeeder for
     * the same convention) for the three existing partner pages: Group
     * JOOS, KONA I and VERVE. Idempotent: each section is only written if
     * it's currently empty, so re-running this after someone has started
     * editing in /admin won't clobber their changes.
     *
     * Every image field (hero backgrounds, partner logos, facility/site/
     * certificate photos) is left blank on purpose — a seeder can't
     * fabricate real image files, and per the client's own instruction
     * these are being uploaded by hand via each dedicated /admin editor.
     */
    public function run(): void
    {
        $this->seedGroupJoos();
        $this->seedKonaI();
        $this->seedVerve();
    }

    protected function seedGroupJoos(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'group-joos'],
            ['template' => 'partner', 'status' => 'draft', 'order' => 1, 'title_fr' => 'Group JOOS']
        );

        $content = $page->content ?? [];

        if (blank($page->title_en)) {
            $page->title_en = 'Group JOOS';
        }
        if ($page->status !== 'published') {
            $page->status = 'published';
        }
        $page->order = 1;

        if (empty(data_get($content, 'introduction.text.fr'))) {
            $content['introduction']['logo'] = null;
            $content['introduction']['text'] = [
                'fr' => '<p>Group JOOS est le partenaire de référence de NSI Group pour les solutions documents sécurisés. S’appuyant sur 80 années d’expérience, le groupe JOOS, basé en Belgique, est un leader en production de documents sécurisés en Europe et dans le monde. Ce groupe familial a fait ses preuves dans le temps en termes de contrôle qualité, respect de l’environnement et de la sécurité à tout point de vue. Ce qui lui vaut la détention de certifications internationales reconnues telle qu’INTERGRAF ISO 14298.</p><p>JOOS produit tous les documents de valeurs des clients de NSI Group, et confère à ce dernier la représentation exclusive de JOOS comme franchise dans tous les pays d’Afrique où NSI Group est présent.</p>',
                'en' => '<p>Group JOOS is NSI Group\'s reference partner for secure document solutions. Drawing on 80 years of experience, the JOOS group, based in Belgium, is a leader in secure document production across Europe and worldwide. This family-owned group has a long track record on quality control, environmental responsibility, and security at every level — earning it internationally recognised certifications such as INTERGRAF ISO 14298.</p><p>JOOS produces all of NSI Group\'s clients\' value documents, and grants NSI Group exclusive representation of JOOS as a franchise in every African country where NSI Group operates.</p>',
            ];
        }

        if (empty(data_get($content, 'facts'))) {
            $facts = [
                ['fr' => 'Plus de 80 ans d’expérience.', 'en' => 'Over 80 years of experience.'],
                ['fr' => 'Structure familiale.', 'en' => 'Family-owned structure.'],
                ['fr' => 'Clientèle fidèle.', 'en' => 'Loyal customer base.'],
                ['fr' => 'Management supervisé par un conseil d’administration externe.', 'en' => 'Management overseen by an external board of directors.'],
                ['fr' => 'Diversité de produits et de services.', 'en' => 'Diversity of products and services.'],
                ['fr' => 'Bonne santé financière.', 'en' => 'Strong financial health.'],
                ['fr' => 'Recherche et développement.', 'en' => 'Research and development.'],
                ['fr' => 'Leader européen.', 'en' => 'European leader.'],
                ['fr' => 'Fait partie d’un groupement d’imprimeries (EFORMA).', 'en' => 'Part of a printing group (EFORMA).'],
            ];
            $content['facts'] = $facts;
            $content['facility']['image'] = null;
        }

        if (empty(data_get($content, 'certificates.items'))) {
            $content['certificates']['title'] = ['fr' => 'Garanties et Certificats', 'en' => 'Guarantees and Certificates'];
            $content['certificates']['image'] = null;
            $content['certificates']['items'] = [
                ['fr' => 'ISO 14298 (précédemment CWA 14641)', 'en' => 'ISO 14298 (formerly CWA 14641)', 'heading' => false],
                ['fr' => 'Information Security Management', 'en' => 'Information Security Management', 'heading' => false],
                ['fr' => 'System ISO 27001', 'en' => 'ISO 27001 System', 'heading' => false],
                ['fr' => 'QUALITÉ:', 'en' => 'QUALITY:', 'heading' => true],
                ['fr' => 'Quality Management System ISO 9001-2005.', 'en' => 'Quality Management System ISO 9001-2005.', 'heading' => false],
                ['fr' => 'Information Security Management.', 'en' => 'Information Security Management.', 'heading' => false],
                ['fr' => 'System conform ISO 27001 guidelines.', 'en' => 'System conforms to ISO 27001 guidelines.', 'heading' => false],
                ['fr' => 'ENVIRONNEMENT:', 'en' => 'ENVIRONMENT:', 'heading' => true],
                ['fr' => 'FSC', 'en' => 'FSC', 'heading' => false],
                ['fr' => 'PEFC', 'en' => 'PEFC', 'heading' => false],
                ['fr' => 'ISO 14001', 'en' => 'ISO 14001', 'heading' => false],
            ];
        }

        if (empty(data_get($content, 'production_sites.items'))) {
            $content['production_sites']['title'] = ['fr' => '3 sites de production', 'en' => '3 production sites'];
            $content['production_sites']['items'] = [
                ['name' => ['fr' => 'JOOS PRINT plc (Turnhout, Belgique)', 'en' => 'JOOS PRINT plc (Turnhout, Belgium)'], 'image' => null],
                ['name' => ['fr' => 'JOOS CONNECT plc (Turnhout, Belgique)', 'en' => 'JOOS CONNECT plc (Turnhout, Belgium)'], 'image' => null],
                ['name' => ['fr' => 'JOOS HYBRID plc (Herent, Belgique)', 'en' => 'JOOS HYBRID plc (Herent, Belgium)'], 'image' => null],
            ];
        }

        if (empty(data_get($content, 'capacity.points'))) {
            $content['capacity']['title'] = ['fr' => 'Capacité de Production', 'en' => 'Production Capacity'];
            $content['capacity']['points'] = [
                ['fr' => 'Group JOOS utilise 14.000 tonnes papier/an', 'en' => 'Group JOOS uses 14,000 tonnes of paper per year'],
                ['fr' => '12 presses d’impression', 'en' => '12 printing presses'],
                ['fr' => 'Imprimantes digitales', 'en' => 'Digital printers'],
                ['fr' => 'Diverses machines de façonnage', 'en' => 'Various finishing machines'],
                ['fr' => 'Disaster Recovery Plan entre les sites de production', 'en' => 'Disaster Recovery Plan across production sites'],
                ['fr' => 'Capacité: 3 millions de pages/jour', 'en' => 'Capacity: 3 million pages/day'],
                ['fr' => 'Capacité: 500.000 envois/jour', 'en' => 'Capacity: 500,000 mailings/day'],
                ['fr' => 'Imprimantes laser « hauts-volumes »', 'en' => 'High-volume laser printers'],
                ['fr' => '15 machines de mise sous plis', 'en' => '15 enveloping machines'],
            ];
            $content['capacity']['images'] = [];
        }

        if (empty(data_get($content, 'secure_document_certification.text.fr'))) {
            $content['secure_document_certification']['title'] = [
                'fr' => 'Certification d\'Impression de Documents Sécurisés',
                'en' => 'Secure Document Printing Certification',
            ];
            $content['secure_document_certification']['text'] = [
                'fr' => '<p><strong>INTERGRAF ISO 14298</strong><br>Group JOOS a obtenu le certificat ISO 14298 d\'Intergraf. Ce certificat reconnu au niveau international indique que JOOS a introduit un système de sécurisation et qu\'il le maintient à niveau.</p>',
                'en' => '<p><strong>INTERGRAF ISO 14298</strong><br>Group JOOS has obtained the ISO 14298 certificate from Intergraf. This internationally recognised certificate confirms that JOOS has introduced a security management system and maintains it to standard.</p>',
            ];
            $content['secure_document_certification']['image'] = null;
        }

        $page->content = $content;
        $page->save();

        $this->command?->info('Group JOOS content seeded (introduction, facts, certificates, production sites, capacity, secure document certification).');
    }

    protected function seedKonaI(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'kona-i'],
            ['template' => 'partner', 'status' => 'draft', 'order' => 2, 'title_fr' => 'KONA I']
        );

        $content = $page->content ?? [];

        if (blank($page->title_en)) {
            $page->title_en = 'KONA I';
        }
        if ($page->status !== 'published') {
            $page->status = 'published';
        }
        $page->order = 2;

        if (empty(data_get($content, 'paragraphs'))) {
            $content['introduction']['logo'] = null;
            $content['paragraphs'] = [
                [
                    'fr' => 'Kona I est une société technologique qui fournit des solutions et des plates-formes complètes au marché mondial des Fin-tech. Depuis la création de la société en 1998, Kona I a développé et commercialisé le premier système intégré de transport par carte bus-métro en Corée.',
                    'en' => 'Kona I is a technology company that provides complete solutions and platforms to the global Fin-tech market. Since the company\'s founding in 1998, Kona I has developed and commercialised the first integrated bus-metro transit card system in Korea.',
                ],
                [
                    'fr' => 'Depuis 2004, Kona I fournit les puces IC à l’industrie des télécommunications, à l’industrie bancaire et aux gouvernements à l’aide de son système exclusif d’exploitation de puce KONA.',
                    'en' => 'Since 2004, Kona I has supplied IC chips to the telecommunications industry, the banking industry, and governments using its exclusive KONA chip operating system.',
                ],
                [
                    'fr' => 'NSI Group utilise les cartes Kona I qui sont entièrement conformes à la norme EMV. Les cartes Kona I basées sur la technologie Java Card permettent à NSI Group de développer et de fournir rapidement des applications de cartes à puce pour répondre aux demandes des clients.',
                    'en' => 'NSI Group uses Kona I cards, which are fully EMV compliant. Kona I cards, based on Java Card technology, allow NSI Group to quickly develop and deliver smart card applications to meet client demands.',
                ],
            ];
        }

        $page->content = $content;
        $page->save();

        $this->command?->info('KONA I content seeded (introduction paragraphs).');
    }

    protected function seedVerve(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'verve'],
            ['template' => 'partner', 'status' => 'draft', 'order' => 3, 'title_fr' => 'VERVE']
        );

        $content = $page->content ?? [];

        if (blank($page->title_en)) {
            $page->title_en = 'VERVE';
        }
        if ($page->status !== 'published') {
            $page->status = 'published';
        }
        $page->order = 3;

        if (empty(data_get($content, 'paragraphs'))) {
            $content['introduction']['logo'] = null;
            $content['paragraphs'] = [
                [
                    'fr' => 'Verve International est une multinationale Nigérienne de technologie financière et une marque Pan-Africaine de cartes de paiement appartenant à Interswitch Group.',
                    'en' => 'Verve International is a Nigerian financial technology multinational and a Pan-African payment card brand owned by Interswitch Group.',
                ],
                [
                    'fr' => 'Le partenariat entre NSI Group et Interswitch Group se fonde sur une vision partagée d’une «croissance africaine portée par les entreprises africaines» et une complémentarité territoriale qui relie l’Afrique anglophone à l’Afrique francophone.',
                    'en' => 'The partnership between NSI Group and Interswitch Group is built on a shared vision of "African growth driven by African companies" and a territorial complementarity connecting English-speaking Africa with French-speaking Africa.',
                ],
                [
                    'fr' => 'Verve est la carte de paiement la plus utilisée au Nigeria, représentant 18 millions des 25 millions de cartes en circulation dans le pays. Verve a également été lancé au Kenya et sera bientôt lancé dans d’autres pays de l’Afrique de l’Est.',
                    'en' => 'Verve is the most widely used payment card in Nigeria, accounting for 18 million of the 25 million cards in circulation in the country. Verve has also launched in Kenya and will soon launch in other East African countries.',
                ],
            ];
        }

        $page->content = $content;
        $page->save();

        $this->command?->info('VERVE content seeded (introduction paragraphs).');
    }
}
