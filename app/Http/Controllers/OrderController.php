<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

use App\Services\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:event_tables,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = $this->orderService->createOrder(
                $validated['table_id'],
                $validated['items']
            );

            return response()->json(
                ['success' => true, 'order_id' => $order->id]
            );
        } catch (Exception $e) {
            return response()->json(
                ['success' => false, 'message' => 'Error creating order.'],
                500
            );
        }
    }
}
