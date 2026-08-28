<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\TranslationString;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One-time offline migration: reads the OLD WordPress database (imported
 * locally from nsidatabase_backup.sql into a throwaway DB — see SETUP.md)
 * via a secondary Laravel DB connection named 'legacy', and writes clean
 * data into the new schema.
 *
 * This never touches the live old website — it's a local, disconnected
 * data import, run once, on your machine.
 *
 * Usage: php artisan migrate:legacy-content
 */
class MigrateLegacyContent extends Command
{
    protected $signature = 'migrate:legacy-content {--dry-run : Preview without writing}';

    protected $description = 'Import content from the old WordPress DB dump into the new schema';

    /**
     * Maps: new slug => [fr WP post_name, en WP post_name, template]
     * Built from the page inventory extracted from the old site's ns_posts table.
     * FR and EN post_names differ (Polylang pattern) — that's exactly what we're fixing.
     */
    protected array $pageMap = [
        'home' => ['fr' => 'accueil', 'en' => 'home', 'template' => 'home'],
        'nsi-group' => ['fr' => 'nsi-groupe', 'en' => 'nsi-group', 'template' => 'about'],
        'references' => ['fr' => 'references-commerciales', 'en' => 'references', 'template' => 'references'],
        'group-joos' => ['fr' => 'partenaire-commercial-groupe-joos', 'en' => 'business-partner-group-joos', 'template' => 'partner'],
        'solutions-monetiques' => ['fr' => 'solutions-monetiques', 'en' => 'electronic-banking-solutions', 'template' => 'solution'],
        'software-expertise' => ['fr' => 'expertise-logiciel', 'en' => 'software-expertise', 'template' => 'solution'],
        'consulting-advisory' => ['fr' => 'consulting-advisory-services', 'en' => 'consulting-advisory-services', 'template' => 'solution'],
        'bank-card' => ['fr' => 'carte-bancaire', 'en' => 'payment-cards', 'template' => 'product'],
        'secured-documents' => ['fr' => 'documents-securises', 'en' => 'secured-documents', 'template' => 'product'],
        'contact' => ['fr' => 'nous-contacter', 'en' => null, 'template' => 'contact'],
        'legal-notices' => ['fr' => 'mentions-legales', 'en' => 'legal-notices', 'template' => 'legal'],
        'jobs' => ['fr' => 'emplois', 'en' => 'jobs', 'template' => 'jobs'],
    ];

    /**
     * Job postings in the old site were individual WP 'post' entries
     * (not page content) — e.g. "Développeur Commercial Paiements
     * Electroniques (H/F)" / its English counterpart. Unlike Press,
     * there's no reliable category/taxonomy signal to auto-detect them
     * from the dump alone, so list the FR/EN post_name slug pairs here
     * after checking the old site's post list once (SETUP.md has the
     * SQL query to find them). Same underlying shape as Press, so they
     * reuse press_posts/press_post_translations with type='job'.
     */
    protected array $jobPostMap = [
        'commercial-paiements-electroniques' => [
            'fr' => 'developpeur-commercial-paiements-electroniques-h-f',
            'en' => 'electronic-payments-sales-representative-m-f',
        ],
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if (! config('database.connections.legacy')) {
            $this->error("No 'legacy' DB connection configured. Add it to config/database.php first — see SETUP.md.");

            return self::FAILURE;
        }

        foreach ($this->pageMap as $slug => $map) {
            $this->info("Processing: {$slug}");

            $page = $dryRun ? null : Page::firstOrCreate(
                ['slug' => $slug],
                ['template' => $map['template'], 'status' => 'draft', 'order' => 0]
            );

            $localeBlocks = [];
            $localeTitles = [];

            foreach (['fr', 'en'] as $locale) {
                $postName = $map[$locale] ?? null;

                if (! $postName) {
                    $this->warn("  [{$locale}] no legacy page mapped — will need manual content entry");

                    continue;
                }

                $legacyPost = DB::connection('legacy')->table('ns_posts')
                    ->where('post_name', $postName)
                    ->where('post_type', 'page')
                    ->where('post_status', 'publish')
                    ->first();

                if (! $legacyPost) {
                    $this->warn("  [{$locale}] legacy post '{$postName}' not found");

                    continue;
                }

                $blocks = $this->htmlToBlocks($legacyPost->post_content);

                if (empty($blocks) && trim(strip_tags($legacyPost->post_content)) !== '') {
                    $blocks = [['type' => 'raw_html', 'html' => $legacyPost->post_content]];
                    $this->warn("  [{$locale}] parser found 0 structured blocks — kept as raw_html, needs manual conversion");
                }

                $localeBlocks[$locale] = $blocks;
                $localeTitles[$locale] = $legacyPost->post_title;
                $this->line("    [{$locale}] title: {$legacyPost->post_title}, blocks: ".count($blocks));
            }

            $merged = $this->mergeLocaleBlocks($localeBlocks['fr'] ?? [], $localeBlocks['en'] ?? []);
            $needsReview = collect($merged)->where('needs_review', true)->count();

            if ($needsReview > 0) {
                $this->warn("  {$needsReview} block(s) flagged needs_review — structural mismatch between FR/EN at that position, needs a human to pair them correctly");
            }

            if (! $dryRun) {
                $page->update([
                    'title_fr' => $localeTitles['fr'] ?? $page->title_fr,
                    'title_en' => $localeTitles['en'] ?? null,
                    'content' => ['blocks' => $merged],
                ]);
            }
        }

        $this->importTranslationStrings($dryRun);
        $this->importJobPostings($dryRun);

        $this->newLine();
        $this->info($dryRun
            ? 'Dry run complete. Review output above, then run without --dry-run.'
            : 'Migration complete. Review flagged blocks in /admin.');

        return self::SUCCESS;
    }

    /**
     * Merges previously-separate FR and EN block arrays (from the old
     * per-locale migration) into one array with inline {fr, en} pairs.
     * Blocks are paired by position. If a block is genuinely missing on
     * one side, that's just "not translated yet" (empty en/fr, not
     * flagged). If both sides exist at that position but have DIFFERENT
     * types, that's a real structural conflict — flagged for a human to
     * sort out rather than guessed at.
     */
    protected function mergeLocaleBlocks(array $fr, array $en): array
    {
        $count = max(count($fr), count($en));
        $merged = [];

        for ($i = 0; $i < $count; $i++) {
            $f = $fr[$i] ?? null;
            $e = $en[$i] ?? null;

            if ($f && $e && ($f['type'] ?? null) === ($e['type'] ?? null)) {
                $merged[] = $this->pairBlock($f, $e);
            } elseif ($f && ! $e) {
                $merged[] = $this->pairBlock($f, ['type' => $f['type']]);
            } elseif (! $f && $e) {
                $merged[] = $this->pairBlock(['type' => $e['type']], $e);
            } else {
                $merged[] = [
                    'type' => $f['type'] ?? $e['type'] ?? 'text',
                    'fr_raw' => $f,
                    'en_raw' => $e,
                    'needs_review' => true,
                ];
            }
        }

        return $merged;
    }

    protected function pairBlock(array $f, array $e): array
    {
        return match ($f['type']) {
            'heading', 'text' => [
                'type' => $f['type'],
                'text' => ['fr' => $f['text'] ?? '', 'en' => $e['text'] ?? ''],
            ],
            'list' => [
                'type' => 'list',
                'items' => $this->pairListItems($f['items'] ?? [], $e['items'] ?? []),
            ],
            'raw_html' => [
                'type' => 'raw_html',
                'html' => ['fr' => $f['html'] ?? '', 'en' => $e['html'] ?? ''],
                'needs_review' => true,
            ],
            default => ['type' => $f['type'], 'fr_raw' => $f, 'en_raw' => $e, 'needs_review' => true],
        };
    }

    protected function pairListItems(array $frItems, array $enItems): array
    {
        $count = max(count($frItems), count($enItems));
        $paired = [];

        for ($i = 0; $i < $count; $i++) {
            $paired[] = ['fr' => $frItems[$i] ?? '', 'en' => $enItems[$i] ?? ''];
        }

        return $paired;
    }

    /**
     * Naive HTML -> content block splitter. This WILL need manual review —
     * the old post_content is raw WP HTML (shortcodes, inline styles, etc).
     * Goal here is a usable first pass, not a perfect parser.
     */
    protected function htmlToBlocks(string $html): array
    {
        $blocks = [];
        $html = strip_tags($html, '<h1><h2><h3><p><ul><li><img>');

        preg_match_all('/<(h[1-3]|p|ul)[^>]*>(.*?)<\/\1>/is', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            [$full, $tag, $inner] = $match;

            if (str_starts_with($tag, 'h')) {
                $blocks[] = ['type' => 'heading', 'text' => trim(strip_tags($inner))];
            } elseif ($tag === 'ul') {
                preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $inner, $items);
                $blocks[] = ['type' => 'list', 'items' => array_map(fn ($i) => trim(strip_tags($i)), $items[1])];
            } else {
                $text = trim(strip_tags($inner));
                if ($text !== '') {
                    $blocks[] = ['type' => 'text', 'text' => $text];
                }
            }
        }

        return $blocks;
    }

    /**
     * Pulls the TranslatePress dictionary rows into the new translation_strings
     * table. Rows with an empty 'translated' column become rows with `en = null`
     * — visible in the admin as missing, instead of silently blank.
     */
    /**
     * Pulls the TranslatePress dictionary rows into the new translation_strings
     * table. Rows with an empty 'translated' column become rows with `en = null`
     * — visible in the admin as missing, instead of silently blank.
     *
     * IMPORTANT: TranslatePress captured every visible text node on the site,
     * not just reusable UI strings — many rows are full paragraphs that
     * duplicate content already migrated into page_translations.content_blocks.
     * Only strings under SHORT_STRING_LIMIT chars are treated as genuine
     * reusable UI text (nav labels, buttons, footer). Longer ones are skipped
     * here and logged, since they belong in page content, not the global
     * string table — importing them as "strings" would just create confusing
     * duplicate entries in the admin.
     */
    protected const SHORT_STRING_LIMIT = 120;

    protected function importTranslationStrings(bool $dryRun): void
    {
        $this->newLine();
        $this->info('Importing global UI strings from TranslatePress dictionary...');

        $rows = DB::connection('legacy')->table('ns_trp_dictionary_fr_fr_en_us')->get();
        $missing = 0;
        $imported = 0;
        $skippedLong = 0;
        $suspectCount = 0;

        foreach ($rows as $row) {
            if (mb_strlen($row->original) > self::SHORT_STRING_LIMIT) {
                $skippedLong++;

                continue; // likely page content, not a reusable UI string
            }

            // Short + collision-safe key: truncated slug + hash suffix,
            // comfortably under the 255-char column limit either way.
            $slugPart = Str::limit(Str::slug($row->original, '_'), 60, '');
            $key = 'legacy.'.$slugPart.'_'.substr(md5($row->original), 0, 8);

            $en = trim((string) $row->translated) !== '' ? $row->translated : null;
            $suspect = $this->looksLikeEnglish($row->original);

            if ($en === null) {
                $missing++;
            }
            if ($suspect) {
                $suspectCount++;
            }
            $imported++;

            if (! $dryRun) {
                TranslationString::updateOrCreate(
                    ['key' => $key],
                    [
                        'group' => 'legacy',
                        'fr' => $row->original,
                        'en' => $en,
                        'suspect_source_language' => $suspect,
                    ]
                );
            }
        }

        $this->line("  {$rows->count()} total rows in old dictionary.");
        $this->line("  {$imported} imported as UI strings, {$missing} missing English translation (flagged in admin).");
        $this->line("  {$suspectCount} flagged as suspect_source_language — the 'fr' value looks like it's actually English (TRP scanned the EN page and mislabeled it). These need manual review before trusting them as canonical French.");
        $this->line("  {$skippedLong} skipped — over ".self::SHORT_STRING_LIMIT." chars, treated as page content (already covered by page_translations, not global strings).");
    }

    /**
     * Crude heuristic ONLY — flags rows for human review, never auto-corrects.
     * French text almost always contains either an accented character or a
     * common French function word; English short strings (menu labels,
     * button text) typically contain neither. False positives/negatives are
     * expected and fine — this is a triage aid, not a translator.
     */
    protected function looksLikeEnglish(string $text): bool
    {
        $hasAccent = (bool) preg_match('/[éèêëàâäùûüçîïôœ]/i', $text);
        if ($hasAccent) {
            return false;
        }

        $frenchStopwords = '/\b(le|la|les|un|une|des|du|de|et|est|dans|pour|avec|sur|nous|vous|votre|notre|au|aux|ce|cette|ces|qui|que)\b/i';
        if (preg_match($frenchStopwords, $text)) {
            return false;
        }

        // No French signal found in a string that isn't a single ambiguous
        // word (e.g. "Contact" could be either language) — flag it.
        return str_word_count($text) >= 2 || in_array(strtolower(trim($text)), [
            'home', 'about', 'jobs', 'press', 'products', 'solutions', 'partners', 'contact', 'website',
        ], true);
    }

    /**
     * Job postings, using the same PressPost/PressPostTranslation shape
     * as press articles, distinguished by type='job'.
     */
    protected function importJobPostings(bool $dryRun): void
    {
        $this->newLine();

        if (empty($this->jobPostMap)) {
            $this->warn('No job postings mapped in $jobPostMap yet — skipping. '
                .'Run the SQL query in SETUP.md against nordsud_legacy to find job post slugs, '
                .'add them to $jobPostMap, then re-run this command.');

            return;
        }

        $this->info('Importing job postings...');

        foreach ($this->jobPostMap as $slug => $map) {
            $post = $dryRun ? null : \App\Models\PressPost::firstOrCreate(
                ['slug' => $slug],
                ['type' => 'job', 'status' => 'draft']
            );

            foreach (['fr', 'en'] as $locale) {
                $postName = $map[$locale] ?? null;
                if (! $postName) {
                    continue;
                }

                $legacyPost = DB::connection('legacy')->table('ns_posts')
                    ->where('post_name', $postName)
                    ->where('post_type', 'post')
                    ->where('post_status', 'publish')
                    ->first();

                if (! $legacyPost) {
                    $this->warn("  [{$locale}] job post '{$postName}' not found");

                    continue;
                }

                $this->line("  [{$locale}] {$legacyPost->post_title}");

                if (! $dryRun) {
                    \App\Models\PressPostTranslation::updateOrCreate(
                        ['press_post_id' => $post->id, 'locale' => $locale],
                        [
                            'title' => $legacyPost->post_title,
                            'body' => $legacyPost->post_content,
                        ]
                    );
                }
            }
        }
    }
}