<?php

namespace App\Console\Commands;

use App\Models\TranslationString;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Fills the missing English values (and fixes the "suspect source language"
 * flags) of the legacy.* translation strings that were imported from the old
 * TranslatePress dictionary.
 *
 * Data: database/data/legacy_translations.json, keyed by translation key.
 * Each entry has a mode:
 *   - "swap": the stored 'fr' value is really English. The English text moves
 *             to 'en' and the proper French goes to 'fr'. Only applied when the
 *             stored 'fr' still matches what the file expects.
 *   - "en":   'fr' is already French; only 'en' is added. If 'fr' was damaged
 *             by the import ("??" instead of accents) it is repaired.
 *   - "copy": language-neutral (URL, e-mail, person or brand name, number):
 *             'en' becomes a copy of 'fr'.
 *
 * Safe to re-run: rows that already have an English value are never touched,
 * and nothing is deleted. Use --dry-run to preview.
 *
 * Usage: php artisan seed:translations [--dry-run]
 */
class SeedLegacyTranslations extends Command
{
    protected $signature = 'seed:translations {--dry-run : Show what would change without saving}';

    protected $description = 'Seed the missing English/French values of the legacy translation strings';

    public function handle(): int
    {
        $path = database_path('data/legacy_translations.json');

        if (! is_file($path)) {
            $this->error("Data file not found: {$path}");

            return self::FAILURE;
        }

        $entries = json_decode((string) file_get_contents($path), true);

        if (! is_array($entries)) {
            $this->error('Data file is not valid JSON.');

            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');
        $stats = ['swap' => 0, 'en' => 0, 'copy' => 0, 'fr_repaired' => 0, 'already_done' => 0, 'changed' => 0, 'not_found' => 0];
        $notes = [];

        $apply = function () use ($entries, $dry, &$stats, &$notes) {
            foreach ($entries as $key => $entry) {
                $row = TranslationString::where('key', $key)->first();

                if (! $row) {
                    $stats['not_found']++;
                    $notes[] = "not found: {$key}";

                    continue;
                }

                if (filled($row->en)) {
                    $stats['already_done']++;

                    continue;
                }

                $current = (string) $row->fr;
                $update = ['suspect_source_language' => false];

                switch ($entry['mode']) {
                    case 'swap':
                        if ($this->norm($current) !== $this->norm($entry['expect'])) {
                            $stats['changed']++;
                            $notes[] = "skipped (value changed since the backup): {$key} => \"{$current}\"";

                            continue 2;
                        }
                        $update['en'] = $entry['en'] ?? $current;
                        $update['fr'] = $entry['fr'];
                        break;

                    case 'en':
                        $update['en'] = $entry['en'];
                        if (isset($entry['fr_fix']) && preg_match('/[?\x{FFFD}]/u', $current)) {
                            $update['fr'] = $entry['fr_fix'];
                            $stats['fr_repaired']++;
                        }
                        break;

                    case 'copy':
                        $update['en'] = $current;
                        break;

                    default:
                        $notes[] = "unknown mode for {$key}";

                        continue 2;
                }

                $stats[$entry['mode']]++;

                if (! $dry) {
                    $row->update($update);
                }
            }
        };

        $dry ? $apply() : DB::transaction($apply);

        $this->table(['Result', 'Rows'], [
            [$dry ? 'would swap (English moved to EN, French written)' : 'swapped (English moved to EN, French written)', $stats['swap']],
            [$dry ? 'would add English (French kept)' : 'added English (French kept)', $stats['en']],
            [$dry ? 'would copy (language-neutral)' : 'copied (language-neutral)', $stats['copy']],
            ['  of which French accents repaired', $stats['fr_repaired']],
            ['skipped: already had English', $stats['already_done']],
            ['skipped: French value differs from the backup', $stats['changed']],
            ['skipped: key not found', $stats['not_found']],
        ]);

        foreach ($notes as $note) {
            $this->warn($note);
        }

        $this->line('Still missing English: '.TranslationString::whereNull('en')->count());
        $this->line('Still flagged suspect: '.TranslationString::where('suspect_source_language', true)->count());

        if ($dry) {
            $this->info('Dry run — nothing was saved.');
        }

        return self::SUCCESS;
    }

    /** Compare texts ignoring case, punctuation and damaged characters. */
    private function norm(string $text): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(html_entity_decode($text)));
    }
}
