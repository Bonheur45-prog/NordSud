<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PressComment extends Model
{
    use HasFactory;

    protected $fillable = ['press_post_id', 'author_name', 'author_email', 'body', 'commented_at', 'approved'];

    protected $casts = [
        'commented_at' => 'datetime',
        'approved' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (PressComment $comment) {
            $comment->commented_at ??= now();
        });
    }

    public function pressPost(): BelongsTo
    {
        return $this->belongsTo(PressPost::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }
}
