<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PressTag extends Model
{
    use HasFactory;

    protected $fillable = ['name_fr', 'name_en', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (PressTag $tag) {
            if (blank($tag->slug)) {
                $tag->slug = Str::slug($tag->name_fr ?: $tag->name_en);
            }
        });
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(PressPost::class, 'press_post_press_tag');
    }

    public function name(string $locale): ?string
    {
        return $this->{"name_{$locale}"} ?: $this->name_fr;
    }
}
