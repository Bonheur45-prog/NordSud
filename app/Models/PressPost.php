<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PressPost extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'type', 'featured_image', 'published_at', 'status'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PressPostTranslation::class);
    }

    public function translation(string $locale): ?PressPostTranslation
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'fr');
    }

    public function scopePublished($query)
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopePress($query)
    {
        return $query->where('type', 'press');
    }

    public function scopeJobs($query)
    {
        return $query->where('type', 'job');
    }
}