<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSettings extends Model
{
    protected $fillable = [
        'facebook_url',
        'instagram_url',
        'x_url',
        'linkedin_url',
    ];

    /**
     * Only URLs that are actually filled in — so the footer can loop over
     * this and skip rendering an icon for a network left blank, instead
     * of every template needing to check each field individually.
     *
     * @return array<string, string>
     */
    public function links(): array
    {
        return array_filter([
            'facebook' => $this->facebook_url,
            'instagram' => $this->instagram_url,
            'x' => $this->x_url,
            'linkedin' => $this->linkedin_url,
        ]);
    }
}
