<?php

namespace App\Console\Commands;

use App\Models\TranslationString;
use Illuminate\Console\Command;

/**
 * Seeds the fixed UI strings the new layout/nav/footer need (nav labels,
 * footer column headings, buttons) — separate from the legacy-imported
 * strings, since these are new copy for the new site, not migrated from
 * the old one.
 *
 * Usage: php artisan seed:nav-strings
 */
class SeedNavigationStrings extends Command
{
    protected $signature = 'seed:nav-strings';

    protected $description = 'Seed the fixed translation strings the public nav/footer need';

    protected array $strings = [
        'nav.home' => ['group' => 'nav', 'fr' => 'Accueil', 'en' => 'Home'],
        'nav.about' => ['group' => 'nav', 'fr' => 'À Propos', 'en' => 'About'],
        'nav.solutions' => ['group' => 'nav', 'fr' => 'Solutions', 'en' => 'Solutions'],
        'nav.products' => ['group' => 'nav', 'fr' => 'Produits', 'en' => 'Products'],
        'nav.partners' => ['group' => 'nav', 'fr' => 'Partenaires', 'en' => 'Partners'],
        'nav.press' => ['group' => 'nav', 'fr' => 'Presse', 'en' => 'Press'],
        'nav.contact' => ['group' => 'nav', 'fr' => 'Contacts', 'en' => 'Contact'],
        'nav.menu' => ['group' => 'nav', 'fr' => 'Menu', 'en' => 'Menu'],
        'nav.close' => ['group' => 'nav', 'fr' => 'Fermer', 'en' => 'Close'],

        'footer.company' => ['group' => 'footer', 'fr' => 'Entreprise', 'en' => 'Company'],
        'footer.solutions' => ['group' => 'footer', 'fr' => 'Solutions', 'en' => 'Solutions'],
        'footer.get_in_touch' => ['group' => 'footer', 'fr' => 'Nous Contacter', 'en' => 'Get in Touch'],
        'footer.tagline' => ['group' => 'footer', 'fr' => 'Intégrateur Pan-Africain de solutions monétiques.', 'en' => 'Pan-African integrator of electronic payment solutions.'],
        'footer.legal_notices' => ['group' => 'footer', 'fr' => 'Mentions légales', 'en' => 'Legal Notices'],
        'footer.rights_reserved' => ['group' => 'footer', 'fr' => 'Tous droits réservés', 'en' => 'All rights reserved'],

        'cta.contact_us' => ['group' => 'global', 'fr' => 'Contactez-nous', 'en' => 'Contact Us'],
    ];

    public function handle(): int
    {
        foreach ($this->strings as $key => $value) {
            TranslationString::updateOrCreate(['key' => $key], $value);
        }

        $this->info(count($this->strings).' navigation/footer strings seeded.');

        return self::SUCCESS;
    }
}
