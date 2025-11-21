<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

use App\Services\EventTableService;

class EventTableController extends Controller
{
    protected $tableService;

    public function __construct(EventTableService $tableService)
    {
        $this->tableService = $tableService;
    }

    public function validateQr($uuid)
    {
        try {
            $table = $this->tableService->getByUuid($uuid);
            return response()->json(
                ['status' => 'success', 'table' => $table]
            );
        } catch (Exception $e) {
            return response()->json(
                ['status' => 'error', 'message' => $e->getMessage()]
            );
        }
    }
}
