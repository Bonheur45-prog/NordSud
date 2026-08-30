<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\TranslationString;
use Illuminate\Database\Seeder;

class ContactFormSeeder extends Seeder
{
    /**
     * Fills in the remaining contact form fields (only "name" existed) and
     * seeds the structural translation strings the contact page needs
     * (section headings, submit button, fallback field labels). Safe to
     * run more than once — form fields are matched by key and translation
     * strings use updateOrCreate, so re-running won't duplicate anything.
     */
    public function run(): void
    {
        $this->seedFormFields();
        $this->seedTranslationStrings();
    }

    protected function seedFormFields(): void
    {
        $page = Page::where('slug', 'contact')->first();

        if (! $page) {
            $this->command?->warn('No page with slug "contact" found — skipping form fields.');

            return;
        }

        $content = $page->content ?? [];
        $existingFields = data_get($content, 'form.fields', []);

        // Fields to add, keyed so we can skip any that already exist
        // (e.g. "name", which was already configured in admin).
        $wanted = [
            'name' => [
                'key' => 'name',
                'label' => ['fr' => 'Nom :', 'en' => 'Name:'],
                'placeholder_fr' => 'Pré-nom',
                'placeholder_en' => 'First name',
            ],
            'email' => [
                'key' => 'email',
                'label' => ['fr' => 'E-mail :', 'en' => 'Email:'],
                'placeholder_fr' => 'vous@exemple.com',
                'placeholder_en' => 'you@example.com',
            ],
            'phone' => [
                'key' => 'phone',
                'label' => ['fr' => 'Téléphone :', 'en' => 'Phone:'],
                'placeholder_fr' => '+250 7XX XXX XXX',
                'placeholder_en' => '+250 7XX XXX XXX',
            ],
            'company' => [
                'key' => 'company',
                'label' => ['fr' => 'Entreprise :', 'en' => 'Company:'],
                'placeholder_fr' => 'Nom de votre entreprise',
                'placeholder_en' => 'Your company name',
            ],
            'country' => [
                'key' => 'country',
                'label' => ['fr' => 'Pays :', 'en' => 'Country:'],
                'placeholder_fr' => 'Rwanda',
                'placeholder_en' => 'Rwanda',
            ],
            'message' => [
                'key' => 'message',
                'label' => ['fr' => 'Message :', 'en' => 'Message:'],
                'placeholder_fr' => 'Votre message',
                'placeholder_en' => 'Your message',
            ],
        ];

        $existingKeys = collect($existingFields)->pluck('key')->filter()->all();

        $merged = $existingFields;

        foreach ($wanted as $key => $field) {
            if (in_array($key, $existingKeys, true)) {
                continue; // already configured in admin — don't overwrite it
            }

            $merged[] = $field;
        }

        // Keep the canonical order: name, email, phone, company, country, message
        $order = array_flip(array_keys($wanted));
        usort($merged, fn ($a, $b) => ($order[$a['key']] ?? 99) <=> ($order[$b['key']] ?? 99));

        $content['form']['fields'] = array_values($merged);
        $page->content = $content;
        $page->save();

        $this->command?->info('Contact form fields seeded: '.implode(', ', array_column($merged, 'key')));
    }

    protected function seedTranslationStrings(): void
    {
        $strings = [
            'contact.title' => [
                'group' => 'contact',
                'fr' => 'Contact par pays',
                'en' => 'Contact by country',
            ],
            'contact.addresses_heading' => [
                'group' => 'contact',
                'fr' => 'Adresse par pays',
                'en' => 'Address by country',
            ],
            'contact.telephones_heading' => [
                'group' => 'contact',
                'fr' => 'Téléphone par pays',
                'en' => 'Telephone by country',
            ],
            'contact.form_errors_heading' => [
                'group' => 'contact',
                'fr' => 'Veuillez corriger les champs suivants',
                'en' => 'Please correct the following fields',
            ],
            'contact.field_name' => [
                'group' => 'contact',
                'fr' => 'Nom',
                'en' => 'Name',
            ],
            'contact.field_email' => [
                'group' => 'contact',
                'fr' => 'E-mail',
                'en' => 'Email',
            ],
            'contact.field_message' => [
                'group' => 'contact',
                'fr' => 'Message',
                'en' => 'Message',
            ],
            'contact.submit' => [
                'group' => 'contact',
                'fr' => 'Envoyer',
                'en' => 'Send',
            ],
        ];

        foreach ($strings as $key => $values) {
            TranslationString::updateOrCreate(['key' => $key], $values);
        }

        $this->command?->info('Seeded '.count($strings).' contact translation strings.');
    }
}
