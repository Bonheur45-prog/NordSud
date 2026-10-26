<?php

namespace App\Console\Commands;

use App\Models\TranslationString;
use App\Support\MissingTranslations;
use Illuminate\Console\Command;

/**
 * READ-ONLY. Lists every value that has French but no English — in page
 * content, page titles/SEO, press posts, tags, authors, press page settings
 * and the glossary — and writes the list to a JSON file in storage/app.
 *
 * Each item is tagged with the status of its page or post; drafts are
 * listed but not counted as live gaps.
 *
 * It never writes to the database. The JSON file is what the English
 * translations are prepared from.
 *
 * Usage: php artisan audit:translations [--list]
 */
class AuditTranslations extends Command
{
    protected $signature = 'audit:translations {--list : Also print every missing item in the terminal}';

    protected $description = 'List every French value with no English (read-only) and save the list to storage/app';

    public function handle(): int
    {
        $items = MissingTranslations::collect();

        // Live (published) first, then drafts, each alphabetical.
        $bySource = [];
        foreach ($items as $item) {
            $key = (MissingTranslations::isLive($item) ? '0' : '1')."|{$item['source']}: {$item['label']}|{$item['status']}";
            $bySource[$key] = ($bySource[$key] ?? 0) + 1;
        }
        ksort($bySource);

        $this->table(
            ['Where', 'Status', 'Missing English'],
            array_map(function ($key, $count) {
                [, $where, $status] = explode('|', $key);

                return [$where, $status, $count];
            }, array_keys($bySource), $bySource)
        );

        if ($this->option('list')) {
            foreach ($items as $item) {
                $this->line("[{$item['source']} #{$item['id']} {$item['label']} ({$item['status']})] {$item['field']}: ".mb_strimwidth(strip_tags($item['fr']), 0, 90, '…'));
            }
        }

        $file = 'translation_audit_'.now()->format('Y-m-d_His').'.json';
        $path = storage_path("app/{$file}");

        file_put_contents($path, json_encode([
            'generated_at' => now()->toIso8601String(),
            'count' => count($items),
            'items' => $items,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $live = count(array_filter($items, fn (array $item) => MissingTranslations::isLive($item)));

        $this->line("Missing English on published pages / press / settings: {$live}");
        $this->line('Missing English on draft pages / posts (not shown on the site): '.(count($items) - $live));
        $this->line('Missing English in translation strings: '.TranslationString::where(fn ($q) => $q->whereNull('en')->orWhere('en', ''))->count());
        $this->info("Saved: {$path}");
        $this->info('Read-only — nothing in the database was changed.');

        return self::SUCCESS;
    }
}
