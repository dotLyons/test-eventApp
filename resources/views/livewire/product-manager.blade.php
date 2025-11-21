<div class="min-h-screen bg-gray-100 p-4 md:p-8">

    <div class="max-w-4xl mx-auto">

        {{-- Encabezado con botón para volver al Dashboard --}}
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">🍔 Gestión del Menú</h1>
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline font-medium">
                &larr; Ir a la Cocina
            </a>
        </div>

        {{-- SECCIÓN 1: Formulario de Creación --}}
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Nuevo Producto</h2>

            <form wire:submit.prevent="save" class="flex flex-col md:flex-row gap-4 items-end">

                {{-- Input Nombre --}}
                <div class="w-full md:flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del producto</label>
                    <input wire:model="name" type="text" placeholder="Ej: Pizza Especial"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Input URL Imagen --}}
                <div class="w-full md:flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL de la Imagen (ImgBB)</label>
                    <input wire:model="image_url" type="url" placeholder="https://i.ibb.co/..."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('image_url')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Botón Guardar --}}
                <button type="submit"
                    class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition">
                    Guardar
                </button>
            </form>

            {{-- Mensajes Flash --}}
            @if (session()->has('message'))
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-bold">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        {{-- SECCIÓN 2: Lista de Productos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($products as $product)
                <div class="bg-white rounded-xl shadow overflow-hidden group relative">

                    {{-- Imagen del producto --}}
                    <div class="h-48 overflow-hidden bg-gray-200 relative">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                        {{-- Badge de disponibilidad --}}
                        @if (!$product->is_available)
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                <span
                                    class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold uppercase tracking-wide">
                                    Sin Stock
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Info y Controles --}}
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-1">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-400 truncate mb-4">{{ $product->image_url }}</p>

                        <div class="flex justify-between items-center gap-2">
                            {{-- Switch de Stock --}}
                            <button wire:click="toggleStatus({{ $product->id }})"
                                class="flex-1 py-2 px-3 rounded text-sm font-bold transition {{ $product->is_available ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                                {{ $product->is_available ? '🟢 En Stock' : '🔴 Agotado' }}
                            </button>

                            {{-- Botón Eliminar --}}
                            <button wire:click="delete({{ $product->id }})"
                                wire:confirm="¿Seguro que quieres borrar {{ $product->name }}?"
                                class="py-2 px-3 bg-red-50 text-red-600 hover:bg-red-100 rounded text-sm"
                                title="Eliminar">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-400">
                    No hay productos cargados. ¡Agrega el primero arriba!
                </div>
            @endforelse
        </div>

    </div>
</div>
