# NSI Fintech CMS — Setup Guide (Day 1)

## 1. Create the base Laravel project (on your machine, with internet access)

```bash
composer create-project laravel/laravel nsi-fintech
cd nsi-fintech
```

## 2. Drop in these files

Copy this zip's contents into your fresh Laravel project, merging into the matching folders:
- `database/migrations/*` → your project's `database/migrations/`
- `app/Models/*` → `app/Models/`
- `app/Services/*` → `app/Services/`
- `app/Http/Controllers/*` → `app/Http/Controllers/`
- `app/Http/Middleware/*` → `app/Http/Middleware/`
- `app/Console/Commands/*` → `app/Console/Commands/`
- `app/helpers.php` → `app/helpers.php` (new file)
- `routes/web.php` → replace your project's `routes/web.php`

## 3. Register the helpers file

In `composer.json`, add to the `autoload` section:
```json
"autoload": {
    "files": ["app/helpers.php"],
    ...
}
```
Then run `composer dump-autoload`.

## 4. Register the SetLocale middleware

Laravel 11: in `bootstrap/app.php`, inside `->withMiddleware()`:
```php
$middleware->alias(['locale' => \App\Http\Middleware\SetLocale::class]);
```
(Already applied directly in `routes/web.php` via the class name — this alias is optional but handy.)

## 5. Database setup (XAMPP)

In `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nsi_fintech
DB_USERNAME=root
DB_PASSWORD=
```

Create the DB in phpMyAdmin, then:
```bash
php artisan migrate
```

## 6. Legacy content import (one-time, local only)

1. Import `nsidatabase_backup.sql` into a **separate, throwaway** local database, e.g. `nsi_legacy`, via phpMyAdmin.
2. Add a second connection in `config/database.php` under `connections`:
```php
'legacy' => [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'nsi_legacy',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_520_ci',
    'prefix' => '',
],
```
3. Preview first:
```bash
php artisan migrate:legacy-content --dry-run
```
4. Then run for real:
```bash
php artisan migrate:legacy-content
```
5. **Expect to manually clean up content_blocks afterward** — the old post_content is raw WP HTML with shortcodes; the importer does a best-effort extraction, not a perfect one.

## 7. Local vhost (so it matches OVH's document-root rule)

Point XAMPP's Apache vhost at `/path/to/nsi-fintech/public`, NOT the project root — this is the same rule OVH will need, so testing this locally now avoids a deploy-day surprise.

## 8. OVH staging deploy

1. Confirm SSH access details (host, user) from your OVH manager.
2. Set up a **staging subdomain** (e.g. `new.nsi-monetique.com`) in OVH's multisite panel, with its document root pointed at wherever you'll place `public/`.
3. Edit `deploy/deploy.sh` with your real SSH host/user/path.
4. `chmod +x deploy/deploy.sh && ./deploy/deploy.sh`

## 9. Cron (for queued mail — contact form)

In OVH's task scheduler (cron), add, every minute:
```
php /path/to/nsi-fintech/artisan schedule:run >> /dev/null 2>&1
```
And in `app/Console/Kernel.php` (or `routes/console.php` on Laravel 11), schedule:
```php
Schedule::command('queue:work --stop-when-empty')->everyMinute();
```

## Next steps (Day 2+)
- Run the legacy import, review every page in a rough admin/tinker session
- Build the custom admin CRUD (Day 3)
- See project overview conversation for the full 7-day plan
