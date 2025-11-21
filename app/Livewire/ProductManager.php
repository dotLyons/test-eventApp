<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProductManager extends Component
{
    // Definimos las variables limpias, sin atributos raros
    public $name = '';

    public $image_url = '';

    public function render()
    {
        return view('livewire.product-manager', [
            'products' => Product::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function save()
    {
        // SOLUCIÓN: Validación explícita aquí dentro.
        // Esto evita el error de conversión porque pasamos un array directo.
        $validated = $this->validate([
            'name' => 'required|min:3',
            'image_url' => 'required|url',
        ], [
            // Mensajes personalizados (opcional)
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 letras.',
            'image_url.required' => 'La imagen es obligatoria.',
            'image_url.url' => 'Debe ser una URL válida (http://...).',
        ]);

        Product::create([
            'name' => $this->name,
            'image_url' => $this->image_url,
            'is_available' => true,
        ]);

        $this->reset(['name', 'image_url']);
        session()->flash('message', 'Producto creado exitosamente.');
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            session()->flash('message', 'Producto eliminado.');
        }
    }

    public function toggleStatus($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->is_available = ! $product->is_available;
            $product->save();
        }
    }
}
