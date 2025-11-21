<?php

namespace App\Livewire;

use App\Models\EventTable;
use App\Models\Product;
use App\Services\OrderService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')] // Usamos el mismo layout limpio
class GuestMenu extends Component
{
    public $table;      // Objeto de la mesa

    public $cart = [];  // Array: [product_id => cantidad]

    public $isOrderPlaced = false; // Para mostrar pantalla de éxito

    public function mount($uuid)
    {
        $this->table = EventTable::where('uuid', $uuid)->firstOrFail();
    }

    public function render()
    {
        $products = Product::where('is_available', true)->get();

        return view('livewire.guest-menu', [
            'products' => $products,
        ]);
    }

    // --- Lógica del Carrito ---

    public function addToCart($productId)
    {
        if (! isset($this->cart[$productId])) {
            $this->cart[$productId] = 0;
        }
        $this->cart[$productId]++;
    }

    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId]) && $this->cart[$productId] > 0) {
            $this->cart[$productId]--;

            if ($this->cart[$productId] === 0) {
                unset($this->cart[$productId]);
            }
        }
    }

    // --- Enviar Pedido ---

    public function submitOrder(OrderService $orderService)
    {
        if (empty($this->cart)) {
            return;
        }

        $items = [];
        foreach ($this->cart as $id => $qty) {
            $items[] = ['product_id' => $id, 'quantity' => $qty];
        }

        // Guardamos usando el servicio que ya creamos
        $orderService->createOrder($this->table->id, $items);

        // Limpiamos y mostramos éxito
        $this->cart = [];
        $this->isOrderPlaced = true;
    }

    public function newOrder()
    {
        $this->isOrderPlaced = false;
    }
}
