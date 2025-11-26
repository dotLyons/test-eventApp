<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class ProductManager extends Component
{
    use WithFileUploads;

    // Definimos las variables limpias, sin atributos raros
    public $name = '';

    public $photo;

    public $price = 0.0;

    public function render()
    {
        return view('livewire.product-manager', [
            'products' => Product::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'price' => 'required|numeric|min:0',
            'photo' => 'required|image|max:5120', // Máx 5MB, debe ser imagen
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número válido.',
            'price.min' => 'El precio no puede ser negativo.',
            'photo.required' => 'Debes subir una imagen.',
            'photo.image' => 'El archivo debe ser una imagen (JPG, PNG).',
            'photo.max' => 'La imagen no puede pesar más de 5MB.',
        ]);

        // 3. Guardamos la imagen en el disco 'public', carpeta 'products'
        // Esto devuelve el path, ej: "products/askjdhasd.jpg"
        $path = $this->photo->store('products', 'public');

        Product::create([
            'name' => $this->name,
            'price' => $this->price,
            'image_url' => $path, // Guardamos la ruta local en la BD
            'is_available' => true,
        ]);

        $this->reset(['name', 'price', 'photo']); // Limpiamos todo
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
