<?php

namespace App\Livewire;

use App\Services\OrderService;
use Livewire\Component;

class KitchenDashboard extends Component
{
    public function render(OrderService $orderService)
    {
        return view('livewire.kitchen-dashboard', [
            'orders' => $orderService->getPendingOrders(),
        ]);
    }

    public function advanceStatus($orderId, $currentStatus, OrderService $service)
    {
        try {
            $nextStatus = match($currentStatus) {
                'pending' => 'in_preparation',
                'in_preparation' => 'ready',
                'ready' => 'completed',
                default => 'completed'
            };

            $service->updateStatus($orderId, $nextStatus);
            
            session()->flash('message', 'Estado actualizado');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar.');
        }
    }
}
