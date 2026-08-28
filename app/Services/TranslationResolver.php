<?php

namespace App\Services;

use App\Models\TranslationString;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Single source of truth for every UI string (nav, buttons, footer, form
 * labels). Replaces the old TranslatePress dictionary, which silently left
 * strings untranslated with no way to detect it.
 *
 * Usage in Blade:  {{ t('nav.solutions') }}
 * Usage in PHP:    app(TranslationResolver::class)->get('nav.solutions')
 */
class TranslationResolver
{
    /**
     * @return array<string, TranslationString> keyed by `key`
     */
    protected function map(): array
    {
        return Cache::rememberForever('translation_strings.map', function () {
            return TranslationString::all()->keyBy('key')->all();
        });
    }

    public function get(string $key, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $map = $this->map();

        if (! isset($map[$key])) {
            // Missing key entirely — log it so it surfaces in admin/dev,
            // but never break the page render.
            Log::warning("Translation key not found: {$key}");

            return $key;
        }

        $string = $map[$key];
        $value = $string->{$locale} ?? null;

        if (empty($value)) {
            // Never silently show blank text like the old TRP dictionary did.
            // Fall back to French (canonical) and log so it shows up as a
            // "missing translation" item for editors to fix in the admin.
            if ($locale !== 'fr') {
                Log::info("Missing '{$locale}' translation for key: {$key}");
            }

            return $string->fr;
        }

        return $value;
    }

    /**
     * All strings in a group, e.g. 'nav' or 'footer' — used to render
     * a whole menu without N individual lookups.
     */
    public function group(string $group, ?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        return collect($this->map())
            ->filter(fn (TranslationString $s) => $s->group === $group)
            ->map(fn (TranslationString $s) => $s->{$locale} ?: $s->fr)
            ->all();
    }
}
