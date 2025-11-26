<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    protected $fillable = ['name', 'image_url', 'is_available', 'price'];

    protected $casts = ['is_available' => 'boolean'];

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // Si empieza con http, es externa (ImgBB), la devolvemos tal cual
                if (str_starts_with($value, 'http')) {
                    return $value;
                }
                // Si no, es local, le agregamos la ruta del storage
                return asset('storage/' . $value);
            }
        );
    }
}
