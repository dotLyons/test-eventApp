<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['event_table_id', 'amount', 'method'];

    public function table()
    {
        return $this->belongsTo(EventTable::class, 'event_table_id');
    }
}
