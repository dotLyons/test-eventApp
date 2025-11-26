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

    public $price = 0.0;

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
            'price' => 'required|numeric|min:0',
        ], [
            // Mensajes personalizados (opcional)
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 letras.',
            'image_url.required' => 'La imagen es obligatoria.',
            'image_url.url' => 'Debe ser una URL válida (http://...).',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
        ]);

        Product::create([
            'name' => $this->name,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'is_available' => true,
        ]);

        $this->reset(['name', 'image_url', 'price']);
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
