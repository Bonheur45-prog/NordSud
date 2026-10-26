<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Fills the missing English values of page content found by
 * `audit:translations` (published pages only).
 *
 * Data: database/data/content_translations.json — one entry per field:
 *   page   the page slug
 *   field  "title_en" / "meta_title_en" / "meta_description_en" (flat column)
 *          or a dot-path to a {fr, en} pair inside `content`, e.g.
 *          "content.sections.0.bullets.3"
 *   fr     the French value exactly as it was when the audit ran
 *   en     the English to write
 *
 * Safety:
 *   - Only an EMPTY English value is ever written. Anything already
 *     translated is left alone.
 *   - The French value must still be exactly what the audit saw; if it was
 *     edited since, that field is skipped and reported.
 *   - French is never modified, nothing is deleted.
 *   - Before writing, the current title/meta/content of every page that will
 *     change is saved to storage/app/content_translations_backup_<time>.json.
 *   - All writes happen in one transaction.
 *
 * Usage: php artisan seed:content-translations [--dry-run]
 */
class SeedContentTranslations extends Command
{
    protected $signature = 'seed:content-translations {--dry-run : Show what would change without saving}';

    protected $description = 'Fill the missing English values of page content (from content_translations.json)';

    private const FLAT_FIELDS = ['title_en', 'meta_title_en', 'meta_description_en'];

    public function handle(): int
    {
        $path = database_path('data/content_translations.json');

        if (! is_file($path)) {
            $this->error("Data file not found: {$path}");

            return self::FAILURE;
        }

        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data['entries'] ?? null)) {
            $this->error('Data file is not valid JSON or has no "entries" list.');

            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');
        $stats = ['filled' => 0, 'already_done' => 0, 'changed' => 0, 'not_found' => 0];
        $notes = [];

        /** @var array<string, Page> $pages pages with at least one change, by slug */
        $pages = [];

        foreach ($data['entries'] as $entry) {
            $slug = $entry['page'];
            $field = $entry['field'];
            $page = $pages[$slug] ?? Page::where('slug', $slug)->first();

            if (! $page) {
                $stats['not_found']++;
                $notes[] = "page not found: {$slug}";

                continue;
            }

            $result = in_array($field, self::FLAT_FIELDS, true)
                ? $this->fillFlat($page, $field, $entry)
                : $this->fillContent($page, $field, $entry);

            $stats[$result]++;

            if ($result === 'filled') {
                $pages[$slug] = $page;
            } elseif ($result === 'changed') {
                $notes[] = "skipped (French edited since the audit): {$slug} → {$field}";
            } elseif ($result === 'not_found') {
                $notes[] = "field not found: {$slug} → {$field}";
            }
        }

        if (! $dry && $pages !== []) {
            $backup = storage_path('app/content_translations_backup_'.now()->format('Y-m-d_His').'.json');
            $original = Page::whereIn('id', array_map(fn (Page $p) => $p->id, $pages))
                ->get(['id', 'slug', 'title_en', 'meta_title_en', 'meta_description_en', 'content'])
                ->map(fn (Page $p) => [
                    'id' => $p->id,
                    'slug' => $p->slug,
                    'title_en' => $p->title_en,
                    'meta_title_en' => $p->meta_title_en,
                    'meta_description_en' => $p->meta_description_en,
                    'content_raw' => $p->getRawOriginal('content'),
                ]);

            if (file_put_contents($backup, json_encode($original, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
                $this->error("Could not write the backup file ({$backup}). Nothing was saved.");

                return self::FAILURE;
            }

            DB::transaction(function () use ($pages) {
                foreach ($pages as $page) {
                    $page->save();
                }
            });

            $this->info("Backup of the original values: {$backup}");
        }

        $this->table(['Result', 'Fields'], [
            [$dry ? 'would fill English' : 'filled English', $stats['filled']],
            ['skipped: already had English', $stats['already_done']],
            ['skipped: French edited since the audit', $stats['changed']],
            ['skipped: page or field not found', $stats['not_found']],
        ]);

        $this->line('Pages that '.($dry ? 'would change' : 'changed').': '.(implode(', ', array_keys($pages)) ?: 'none'));

        foreach ($notes as $note) {
            $this->warn($note);
        }

        if ($dry) {
            $this->info('Dry run — nothing was saved.');
        }

        return self::SUCCESS;
    }

    /** title_en / meta_title_en / meta_description_en on the pages table. */
    private function fillFlat(Page $page, string $field, array $entry): string
    {
        $frField = substr($field, 0, -3).'_fr';

        if (filled($page->{$field})) {
            return 'already_done';
        }

        if ((string) $page->{$frField} !== $entry['fr']) {
            return 'changed';
        }

        $page->{$field} = $entry['en'];

        return 'filled';
    }

    /** A {fr, en} pair inside the content JSON, addressed by dot-path. */
    private function fillContent(Page $page, string $field, array $entry): string
    {
        $content = is_array($page->content) ? $page->content : [];
        $segments = explode('.', $field);

        if (array_shift($segments) !== 'content') {
            return 'not_found';
        }

        $node = &$content;

        foreach ($segments as $segment) {
            if (! is_array($node) || ! array_key_exists($segment, $node)) {
                return 'not_found';
            }
            $node = &$node[$segment];
        }

        if (! is_array($node) || ! array_key_exists('fr', $node)) {
            return 'not_found';
        }

        if (filled(trim(strip_tags((string) ($node['en'] ?? ''))))) {
            return 'already_done';
        }

        if ((string) $node['fr'] !== $entry['fr']) {
            return 'changed';
        }

        $node['en'] = $entry['en'];
        unset($node);

        $page->content = $content;

        return 'filled';
    }
}
