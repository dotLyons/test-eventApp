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

            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">

                {{-- 1. NOMBRE --}}
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input wire:model="name" type="text" placeholder="Ej: Fernet"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 2. PRECIO --}}
                <div class="col-span-12 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center">
                            <span class="text-gray-500 font-bold">$</span>
                        </div>
                        <input wire:model="price" type="number" step="0.01"
                            class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ml-2"
                            placeholder="0.00">
                    </div>
                    @error('price')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 3. SUBIDA DE IMAGEN --}}
                <div class="col-span-12 md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imagen</label>

                    <div class="flex items-center gap-4">
                        {{-- Input File --}}
                        <label
                            class="flex flex-col items-center px-4 py-2 bg-white text-indigo-600 rounded-lg shadow-sm tracking-wide uppercase border border-blue cursor-pointer hover:bg-indigo-50 transition">
                            <span class="text-xs font-bold">📷 Subir Foto</span>
                            <input type='file' wire:model="photo" class="hidden" accept="image/*" />
                        </label>

                        {{-- Vista Previa (Preview) --}}
                        @if ($photo)
                            <div class="h-12 w-12 rounded overflow-hidden border border-gray-200">
                                <img src="{{ $photo->temporaryUrl() }}" class="object-cover w-full h-full">
                            </div>
                            <span class="text-xs text-green-600 font-bold">¡Cargada!</span>
                        @endif
                    </div>

                    <div wire:loading wire:target="photo" class="text-xs text-indigo-500 mt-1">Subiendo...</div>
                    @error('photo')
                        <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 4. BOTÓN --}}
                <div class="col-span-12 flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit" wire:loading.attr="disabled"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg w-full md:w-auto flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="photo">Guardar Producto</span>
                        <span wire:loading wire:target="photo">Espere...</span>
                    </button>
                </div>
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
