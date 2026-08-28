<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PressPostTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['press_post_id', 'locale', 'title', 'excerpt', 'body', 'updated_by'];

    public function pressPost(): BelongsTo
    {
        return $this->belongsTo(PressPost::class);
    }
}
