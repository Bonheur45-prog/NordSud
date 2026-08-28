<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'slug', 'logo', 'name_fr', 'name_en', 'content', 'order', 'status',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function name(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->{"name_{$locale}"} ?: $this->name_fr;
    }

    /**
     * Resolves an inline {fr, en} pair from the `content` JSON tree,
     * same convention as Page::trans().
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name_fr');
    }
}
