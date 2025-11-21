<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'image_url', 'is_available'];

    protected $casts = ['is_available' => 'boolean'];

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
