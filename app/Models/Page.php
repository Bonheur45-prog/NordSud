<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'template', 'status', 'order',
        'title_fr', 'title_en',
        'meta_title_fr', 'meta_title_en',
        'meta_description_fr', 'meta_description_en',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function title(?string $locale = null): ?string
    {
        return $this->localeField('title', $locale);
    }

    public function metaTitle(?string $locale = null): ?string
    {
        return $this->localeField('meta_title', $locale);
    }

    public function metaDescription(?string $locale = null): ?string
    {
        return $this->localeField('meta_description', $locale);
    }

    protected function localeField(string $base, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $value = $this->{"{$base}_{$locale}"} ?? null;

        // Never silently blank — fall back to French (canonical), same
        // principle as TranslationResolver for global UI strings.
        return $value ?: $this->{"{$base}_fr"};
    }

    public function isMissingTranslation(string $locale = 'en'): bool
    {
        return empty($this->{"title_{$locale}"});
    }

    /**
     * Resolves an inline {fr, en} pair from anywhere inside the `content`
     * JSON tree to the current (or given) locale's value, falling back to
     * French. Use like: $page->trans($page->content['hero']['headline'])
     */
    public function trans(?array $pair, ?string $locale = null): ?string
    {
        if ($pair === null) {
            return null;
        }

        $locale = $locale ?? app()->getLocale();

        return $pair[$locale] ?: ($pair['fr'] ?? null);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}