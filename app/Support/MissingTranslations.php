<?php

namespace App\Support;

use App\Models\GlossaryTerm;
use App\Models\Page;
use App\Models\PressAuthor;
use App\Models\PressPageSettings;
use App\Models\PressPost;
use App\Models\PressTag;

/**
 * READ-ONLY scanner: finds every place where French is filled in but the
 * English counterpart is empty, outside the translation_strings table.
 *
 * Covers:
 *   - pages: title / meta_title / meta_description (flat *_fr / *_en columns)
 *   - pages.content: every inline {fr, en} pair anywhere in the JSON tree
 *   - press posts: title / excerpt / body (French row exists, English row
 *     missing or its field empty)
 *   - press tags, press authors, press page settings, glossary terms
 *     (flat *_fr / *_en columns)
 *
 * Nothing here writes to the database. Used by `audit:translations` and by
 * the dashboard's "Missing English translations" stat.
 */
class MissingTranslations
{
    /**
     * @return list<array{source: string, id: int, label: string, field: string, fr: string}>
     */
    public static function collect(): array
    {
        $items = [];

        foreach (Page::orderBy('id')->get() as $page) {
            foreach (['title', 'meta_title', 'meta_description'] as $base) {
                if (self::isMissing($page->{"{$base}_fr"}, $page->{"{$base}_en"})) {
                    $items[] = self::item('page', $page->id, $page->slug, "{$base}_en", $page->{"{$base}_fr"});
                }
            }

            self::walk(is_array($page->content) ? $page->content : [], 'content', function (string $path, string $fr) use (&$items, $page) {
                $items[] = self::item('page', $page->id, $page->slug, $path, $fr);
            });
        }

        foreach (PressPost::with('translations')->orderBy('id')->get() as $post) {
            $fr = $post->translations->firstWhere('locale', 'fr');

            if (! $fr) {
                continue;
            }

            $en = $post->translations->firstWhere('locale', 'en');

            foreach (['title', 'excerpt', 'body'] as $field) {
                if (self::isMissing($fr->{$field}, $en?->{$field})) {
                    $items[] = self::item('press_post', $post->id, (string) $post->slug, $field, $fr->{$field});
                }
            }
        }

        $flatTables = [
            // [model, source name, translatable bases, column used as a readable label]
            [PressTag::class, 'press_tag', ['name'], 'slug'],
            [PressAuthor::class, 'press_author', ['role', 'bio'], 'name'],
            [PressPageSettings::class, 'press_page_settings', ['eyebrow', 'heading', 'hero_subtitle', 'description'], null],
            [GlossaryTerm::class, 'glossary_term', ['term'], 'term_fr'],
        ];

        foreach ($flatTables as [$model, $source, $bases, $labelColumn]) {
            foreach ($model::orderBy('id')->get() as $row) {
                foreach ($bases as $base) {
                    if (self::isMissing($row->{"{$base}_fr"}, $row->{"{$base}_en"})) {
                        $label = $labelColumn ? (string) $row->{$labelColumn} : "#{$row->id}";
                        $items[] = self::item($source, $row->id, $label, "{$base}_en", $row->{"{$base}_fr"});
                    }
                }
            }
        }

        return $items;
    }

    public static function count(): int
    {
        return count(self::collect());
    }

    /**
     * Recurse through a page's content tree. A node with an 'fr' key holding
     * a string is a translatable leaf; anything else is walked into.
     */
    private static function walk(array $node, string $path, callable $onMissing): void
    {
        if (array_key_exists('fr', $node) && ! is_array($node['fr'])) {
            if (self::isMissing($node['fr'], $node['en'] ?? null)) {
                $onMissing($path, (string) $node['fr']);
            }

            return;
        }

        foreach ($node as $key => $child) {
            if (is_array($child)) {
                self::walk($child, "{$path}.{$key}", $onMissing);
            }
        }
    }

    /**
     * French has real text and English has none. "Empty HTML" such as
     * <p></p> or <p>&nbsp;</p> (what a cleared rich editor saves) counts as
     * empty on both sides; an English value holding only an image does not.
     */
    private static function isMissing(mixed $fr, mixed $en): bool
    {
        return self::hasText($fr) && ! self::hasText($en) && ! (is_string($en) && stripos($en, '<img') !== false);
    }

    private static function hasText(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $text = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return preg_replace('/[\s\x{00A0}]+/u', '', $text) !== '';
    }

    private static function item(string $source, int $id, string $label, string $field, string $fr): array
    {
        return compact('source', 'id', 'label', 'field', 'fr');
    }
}
