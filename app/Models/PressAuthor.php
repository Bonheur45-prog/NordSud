<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PressAuthor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'role_fr', 'role_en', 'bio_fr', 'bio_en', 'avatar',
        'facebook_url', 'instagram_url', 'x_url', 'linkedin_url',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(PressPost::class, 'author_id');
    }

    public function role(string $locale): ?string
    {
        return $this->{"role_{$locale}"} ?: $this->role_fr;
    }

    public function bio(string $locale): ?string
    {
        return $this->{"bio_{$locale}"} ?: $this->bio_fr;
    }

    public function socialLinks(): array
    {
        return array_filter([
            'facebook' => $this->facebook_url,
            'instagram' => $this->instagram_url,
            'x' => $this->x_url,
            'linkedin' => $this->linkedin_url,
        ]);
    }
}
