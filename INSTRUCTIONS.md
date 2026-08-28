# NordSud CMS — files to apply locally

These are ONLY the new/changed files from this session — drop each one into
your project at the SAME relative path (overwriting where a file already
exists). Nothing here touches your database directly except two new
migration files, which are additive (they CREATE new tables — they do not
touch `pages`, `translation_strings`, `press_posts`, or anything you've
already imported).

## 1. Copy the files

Copy every file in this folder into your project (`D:\PROJECTS\NordSud\NordSud`
or wherever your XAMPP project root is), preserving the folder structure:

```
app/Filament/Support/TranslatedField.php                          (NEW)
app/Filament/Pages/PageContent/EditsSinglePage.php                (NEW)
app/Filament/Pages/PageContent/HomePage.php                       (NEW)
app/Models/Partner.php                                            (NEW)
app/Models/ClientReference.php                                    (NEW)
app/Models/User.php                                                (CHANGED — adds canAccessPanel)
database/migrations/2024_01_01_000009_create_client_references_table.php  (NEW)
database/migrations/2024_01_01_000010_create_partners_table.php   (NEW)
app/Filament/Resources/Partners/PartnerResource.php                (NEW)
app/Filament/Resources/Partners/Schemas/PartnerForm.php            (NEW)
app/Filament/Resources/Partners/Tables/PartnersTable.php           (NEW)
app/Filament/Resources/Partners/Pages/ListPartners.php             (NEW)
app/Filament/Resources/Partners/Pages/CreatePartner.php            (NEW)
app/Filament/Resources/Partners/Pages/EditPartner.php              (NEW)
tests/Feature/AdminBootTest.php                                    (NEW, optional)
tests/Feature/PartnerFormTest.php                                  (NEW, optional)
tests/Feature/HomePageContentTest.php                              (NEW, optional)
```

**Important about `app/Models/User.php`**: I changed this file to add one
method (`canAccessPanel`). If you've made your own edits to User.php since
the zip you gave me, don't blindly overwrite — just add this method by hand
instead:

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    // ...existing code...

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
```

## 2. Run the new migrations

In your XAMPP project, with Apache/MySQL running:

```
php artisan migrate
```

**Do NOT run `migrate:fresh`** — that would wipe your already-imported
content (the 408 translation strings, suspect-flagged rows, etc.). Plain
`migrate` only runs the two new migration files and creates two new empty
tables: `client_references` and `partners`.

## 3. Check it in the browser

- Go to `/admin/partners` — you should see an empty Partners list with a
  "New partner" button. Try creating "Group JOOS", "KONA I", and "VERVE"
  from the screenshots you sent me — each can have as much or as little
  filled in as it actually needs (KONA I doesn't need certifications/stats,
  JOOS does).
- Go to `/admin/home-page` — this is the new pattern: no template picker,
  just the Home page's real content top-to-bottom (Hero Slider, Press
  Spotlight, Secondary Banner, Section Highlights, Stats/CTA Banner), each
  section collapsible. Fill in the hero slides and save.

## 4. (Optional) Run the automated tests

If you want to verify these work without manually clicking through forms —
this runs against a temporary in-memory database, completely separate from
your real MySQL data, so it's safe to run anytime:

```
php artisan test --filter=AdminBootTest
php artisan test --filter=PartnerFormTest
php artisan test --filter=HomePageContentTest
```

All 3 should show green/PASS. These are the same tests I ran here before
handing this to you — I'm including them so you can independently confirm
what I'm telling you actually works, not just take my word for it.

## What's deliberately NOT included yet

Per the "do it all, but review as we go" approach — this batch is Home +
Partners only, so you can review the pattern before I replicate it across
the remaining 9 pages (NSI Group, References, Kinshasa Production Center,
Solutions Monétiques, Software Expertise, Consulting Advisory, Bank Card,
Secured Documents, Contact, Legal Notices) and the nav-group/dashboard
polish (items 1, 6, 7 from the earlier plan). Once you've clicked through
Home and Partners and you're happy with the pattern, tell me and I'll
continue with the rest in the same reviewable batches.
