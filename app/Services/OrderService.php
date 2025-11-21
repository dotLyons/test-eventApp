<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Crea una orden completa
     * @param int $tableId - ID de la mesa (validado previamente)
     * @param array $items - Array con formato: [['product_id' => 1, 'quantity' => 2], ...]
     */
    public function createOrder(int $tableId, array $items)
    {
        return DB::transaction(function () use ($tableId, $items) {
            $order = Order::create([
                'event_table_id' => $tableId,
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);

                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'product_name_snapshot' => $product->name,
                    ]);
                }
            }

            return $order;
        });
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);
        return $order;
    }

    public function getPendingOrders()
    {
        return Order::with(['table', 'items'])
                ->whereIn('status', ['pending', 'in_preparation', 'ready'])
                ->orderBy('created_at', 'asc')
                ->get();
    }

    public function completeOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->status = 'completed';
        $order->save();
    }
}