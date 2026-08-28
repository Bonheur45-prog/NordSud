<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TranslationString extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'group', 'fr', 'en', 'suspect_source_language', 'updated_by'];

    protected static function booted(): void
    {
        // Any change invalidates the cached lookup map used by TranslationResolver.
        static::saved(fn () => Cache::forget('translation_strings.map'));
        static::deleted(fn () => Cache::forget('translation_strings.map'));
    }

    public function isMissing(string $locale): bool
    {
        return $locale !== 'fr' && empty($this->{$locale});
    }
}