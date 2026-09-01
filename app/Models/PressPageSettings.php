<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PressPageSettings extends Model
{
    protected $fillable = [
        'hero_background_image',
        'eyebrow_fr', 'eyebrow_en',
        'heading_fr', 'heading_en',
        'hero_subtitle_fr', 'hero_subtitle_en',
        'description_fr', 'description_en',
    ];
}
