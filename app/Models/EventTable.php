<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventTable extends Model
{
    protected $fillable = ['name', 'uuid'];

    // Relacion: Una Mesa tiene muchas ordenes
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
