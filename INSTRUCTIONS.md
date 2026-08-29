# Batch: Critical fixes + conflicting-editors resolution

## What this fixes

1. **The whole admin panel was broken.** `app/Filament/AdminPanelProvider.php`
   declared `namespace App\Providers\Filament` but sat in the wrong folder.
   Composer's autoloader explicitly skips files like this (you can see it
   yourself: run `composer dump-autoload` on your current code and read the
   warning it prints). Since `bootstrap/providers.php` tells Laravel to load
   `App\Providers\Filament\AdminPanelProvider`, and that class was
   unloadable, the admin panel could not boot. Same bug, same fix, for
   `CmsOverviewWidget`.

2. **Any artisan command could crash before migrations ran.** The old
   `AdminPanelProvider` queried the database directly, unguarded, every
   time the panel booted — including moments before the `pages` table
   exists. Now wrapped so only that specific "table missing yet" case is
   handled quietly; anything else still surfaces normally.

3. **The real bug you asked me to fix**: `PartnerResource`/`ProductResource`
   (the generic "add a new one" forms) and the dedicated pages you already
   have (`GroupJoosPage`, `KonaIPage`, `VervePage`, `BankCardPage`,
   `SecuredDocumentsPage`) were BOTH able to edit the exact same database
   rows, using two different, incompatible content shapes. Opening Group
   JOOS through the generic form and hitting Save would have silently
   wiped its certificates/production-sites/capacity data. Now:
   - Group JOOS, KONA I, VERVE, Bank Card, Secured Documents are edited
     ONLY through their dedicated page — the generic resource can no
     longer even reach those rows (verified by a real test: it 404s).
   - A brand-new partner or product you add later (no dedicated page
     exists for it yet) is still fully editable through the generic
     "+ Add New Partner" / "+ Add New Product" flow, exactly as before.
   - The sidebar routes each item to whichever editor is actually
     correct for it automatically — you don't have to remember which is
     which.

## Files to copy in (overwrite at the same path)

```
app/Providers/Filament/AdminPanelProvider.php   (NEW location — see below)
app/Filament/Widgets/CmsOverviewWidget.php       (NEW location — see below)
app/Filament/Support/DedicatedPageRegistry.php   (NEW file)
app/Filament/Resources/Partners/PartnerResource.php    (CHANGED)
app/Filament/Resources/Products/ProductResource.php    (CHANGED)
tests/Feature/PartnerEditorConflictTest.php      (NEW — proves the fix)
tests/Feature/AdminBootTest.php                  (CHANGED — removed a
                                                    stale test for a
                                                    route that no longer
                                                    exists on purpose)
```

## Files to DELETE from your project

```
app/Filament/AdminPanelProvider.php          — old, broken location
app/Widgets/CmsOverviewWidget.php            — old, broken location
app/Filament/Resources/Partners/Pages/ListPartners.php   — no longer used
app/Filament/Resources/Products/Pages/ListProducts.php   — no longer used
app/Models/Partner.php                       — orphaned, nothing uses it
```

**Note on `app/Models/Partner.php`**: this is a leftover from an early
version of the project before Partners moved to using the `Page` model.
I checked — nothing in the codebase references it anymore, it's safe to
delete. I did NOT touch its database migration
(`2024_01_01_000010_create_partners_table.php`) or the `partners` table
itself, since dropping a table is a destructive change I won't make
without you explicitly asking for it — if you want that table gone too,
say so and I'll do it properly with a real down-migration.

## No new migrations in this batch

Nothing to run via `php artisan migrate` this time — every change here is
PHP logic, not schema.

## How to verify it worked

1. Copy the files in, delete the ones listed above.
2. `composer dump-autoload` — should now run with NO "does not comply with
   psr-4" warnings (compare to before: you'll see the difference directly).
3. Visit `/admin` — the Partners and Products groups should show your real
   partner/product names (Group JOOS, KONA I, VERVE, etc.) directly in the
   sidebar, plus an "Add New Partner"/"Add New Product" link. No "All
   Partners" list page in between.
4. Try opening Group JOOS from the sidebar — you should land on its full
   dedicated form (Hero, Facts, Certificates, Production Sites, Capacity),
   not a generic logo+paragraphs form.
5. (Optional) Run the tests — these run against a separate in-memory
   database, safe to run anytime:
   ```
   php artisan test --filter=PartnerEditorConflictTest
   php artisan test --filter=AdminBootTest
   ```
   Both should be all green. `PartnerEditorConflictTest` specifically
   proves: Group JOOS is unreachable through the generic editor (blocks
   the data-loss path), still fully editable through its dedicated page,
   and a brand-new partner still works through the generic "+ Add" flow.
