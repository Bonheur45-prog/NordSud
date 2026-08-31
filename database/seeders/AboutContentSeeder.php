<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class AboutContentSeeder extends Seeder
{
    /**
     * Text-only seed for both "about" template pages, transcribed from the
     * old site's screenshots. Idempotent — each page is only touched if
     * its relevant content is currently empty. Images are left blank on
     * every field (no seeder can fabricate real image files); upload
     * those in /admin after running this.
     *
     * English copy for both pages is my own translation, not verified
     * original copy — no old English-language PDF was captured for
     * either of these two pages specifically, unlike some other pages
     * where I had a real English screenshot to work from. Worth a native
     * French speaker/editor review before treating it as final.
     */
    public function run(): void
    {
        $this->seedNsiGroup();
        $this->seedKinshasaProductionCenter();
    }

    protected function seedNsiGroup(): void
    {
        $page = Page::where('slug', 'nsi-group')->first();

        if (! $page) {
            $this->command?->warn('No page with slug "nsi-group" found — skipping.');

            return;
        }

        $content = $page->content ?? [];

        if (empty(data_get($content, 'paragraphs'))) {
            $content['paragraphs'] = [
                [
                    'fr' => "NSI Group est un groupe Pan-Africain spécialisé dans la production, la personnalisation, la gestion et la distribution de documents et supports sécurisés, et de support et solutions monétiques.",
                    'en' => 'NSI Group is a Pan-African group specializing in the production, personalization, management, and distribution of secure documents and media, and in payment support and solutions.',
                ],
                [
                    'fr' => "Fondé en 2003, le Groupe NSI est présent sur 5 pays Africains: Rwanda, Burundi, République Démocratique du Congo (RDC), Djibouti et Tanzanie.",
                    'en' => 'Founded in 2003, NSI Group operates across 5 African countries: Rwanda, Burundi, the Democratic Republic of Congo (DRC), Djibouti, and Tanzania.',
                ],
                [
                    'fr' => "Depuis plus de 15 ans, la croissance du groupe est soutenue et le portefeuille client actuel du groupe est constitué de plus de 60 banques clientes.",
                    'en' => "For over 15 years, the group's growth has been sustained, and its current client portfolio includes more than 60 partner banks.",
                ],
            ];
        }

        $page->content = $content;
        $page->save();

        $this->command?->info('NSI Group page content seeded (3 paragraphs). Hero and body images still need uploading in admin.');
    }

    protected function seedKinshasaProductionCenter(): void
    {
        $page = Page::where('slug', 'centre-de-personnalisation-de-kinshasa-2')->first();

        if (! $page) {
            $this->command?->warn('No page with slug "centre-de-personnalisation-de-kinshasa-2" found — skipping.');

            return;
        }

        $content = $page->content ?? [];

        if (empty(data_get($content, 'opening.text.fr'))) {
            $content['opening']['text'] = [
                'fr' => '<p>Le centre de personnalisation des moyens de paiement de NSI Group à Kinshasa a été construit en 2014 aux normes strictes de sécurité sur un large terrain dans une zone industrielle hautement protégée, nécessitant un important investissement hors équipements de production.</p><p>NSI Group fait appel à une société internationale externe de gardiennage pour disposer d\'agents de sécurité qui assurent une présence 24h/24 et 7j/7. De plus, le centre de production est équipé de caméras de vidéosurveillance, de détecteurs d\'intrusion, de barrières infrarouge et de groupes électrogènes.</p>',
                'en' => '<p>NSI Group\'s payment personalization center in Kinshasa was built in 2014 to strict security standards, on a large site in a highly protected industrial zone, requiring significant investment beyond production equipment alone.</p><p>NSI Group works with an external international security firm to provide guards ensuring a 24/7 presence. The production center is also equipped with color CCTV, intrusion detectors, infrared barriers, and backup generators.</p>',
            ];
        }

        if (empty(data_get($content, 'facility.text.fr'))) {
            $content['facility']['text'] = [
                'fr' => '<p>Construit suivant les normes PCI très strictes de sécurité, le centre de production de NSI Group à Kinshasa a obtenu l\'agrément de la Banque Centrale du Congo (BCC) en tant que Centre de Personnalisation des Cartes Bancaires classé dans la catégorie Prestataire de Services Connexes conformément à l\'Article 9 de l\'Instruction Numéro 42 portant sur les Règles Applicables à la Monétique en RDC.</p><p>Le Centre de Personnalisation des Cartes Bancaires de Kinshasa est soumis à de sévères processus de certification et de contrôle qualité relatifs à l\'importance des pré-requis de sécurité et de sûreté des supports de paiements. Il est en cours de certification par les organismes internationaux Verve, Visa et UnionPay.</p>',
                'en' => '<p>Built to very strict PCI security standards, NSI Group\'s production center in Kinshasa has been approved by the Central Bank of Congo (BCC) as a Bank Card Personalization Center, classified under the Related Service Provider category in accordance with Article 9 of Instruction No. 42 on Payment Card Rules in the DRC.</p><p>The Kinshasa Bank Card Personalization Center is subject to rigorous certification and quality-control processes given the importance of security and payment-media safety requirements. It is currently undergoing certification with the international bodies Verve, Visa, and UnionPay.</p>',
            ];
        }

        if (empty(data_get($content, 'certifications.text.fr'))) {
            $content['certifications']['text'] = [
                'fr' => '<p>Le centre de production de Kinshasa est doté d\'équipements modernes et performants et un programme d\'investissement continu permet de renforcer d\'année en année les capacités de production du centre et de l\'équiper de moyens matériels et logiciels de pointe.</p>',
                'en' => '<p>The Kinshasa production center is equipped with modern, high-performance equipment, and an ongoing investment program strengthens the center\'s production capacity year after year, equipping it with state-of-the-art hardware and software.</p>',
            ];
        }

        // No "closing" content was seeded: the old site's page ends after
        // the certifications block with no separate closing paragraph, so
        // there's nothing real to transcribe here. The field/section
        // exists in the admin editor for whenever you want to add one.

        $page->content = $content;
        $page->save();

        $this->command?->info('Kinshasa Production Center page content seeded (opening, facility, certifications). Images and an optional closing paragraph still need adding in admin.');
    }
}
