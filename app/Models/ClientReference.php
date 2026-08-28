<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientReference extends Model
{
    protected $fillable = ['name', 'logo', 'order'];

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
