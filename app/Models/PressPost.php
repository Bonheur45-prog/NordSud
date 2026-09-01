<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PressPost extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'type', 'author_id', 'featured_image', 'published_at', 'status', 'is_featured'];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PressPostTranslation::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(PressAuthor::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(PressTag::class, 'press_post_press_tag');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PressComment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->where('approved', true)->orderBy('commented_at');
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

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * The chronologically previous and next published press posts, for the
     * "Previous / Next" navigation on the detail page. Null when this post
     * is the oldest/newest.
     */
    public function previousPost(): ?self
    {
        return static::press()->published()
            ->where('published_at', '<', $this->published_at)
            ->orderByDesc('published_at')
            ->first();
    }

    public function nextPost(): ?self
    {
        return static::press()->published()
            ->where('published_at', '>', $this->published_at)
            ->orderBy('published_at')
            ->first();
    }
}