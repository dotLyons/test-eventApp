<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getMenuForGuests()
    {
        return Product::available()->get();
    }

    public function getAllProducts()
    {
        return Product::all();
    }

    public function toggleAvailability($id)
    {
        $product = Product::findOrFail($id);
        $product->is_available = !$product->is_available;
        $product->save();

        return $product;
    }
}