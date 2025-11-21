<?php

namespace App\Services;

use App\Models\EventTable;
use Exception;

class EventTableService
{
    public function getByUuid(string $uuid): ?EventTable
    {
        $table = EventTable::where('uuid', $uuid)->first();

        if (!$table) {
            throw new Exception("Código QR inválido o mesa no encontrada.");
        }

        return $table;
    }

    public function getAll()
    {
        return EventTable::all();
    }
}