<div class="min-h-screen bg-gray-50 pb-24"> {{-- pb-24 para que el botón flotante no tape el final --}}

    {{-- PANTALLA DE ÉXITO (Se muestra al enviar) --}}
    @if ($isOrderPlaced)
        <div
            class="fixed inset-0 bg-green-600 z-50 flex flex-col items-center justify-center text-white p-8 text-center">
            <div class="text-6xl mb-4 animate-bounce">🚀</div>
            <h2 class="text-3xl font-bold mb-2">¡Pedido Enviado!</h2>
            <p class="text-lg opacity-90 mb-8">La cocina ya está preparando tus cosas.</p>

            <button wire:click="newOrder"
                class="bg-white text-green-700 font-bold py-3 px-8 rounded-full shadow-lg hover:scale-105 transition transform">
                Pedir algo más
            </button>
        </div>
    @else
        {{-- HEADER: Nombre de la Mesa --}}
        <div class="sticky top-0 z-40 bg-white shadow-sm px-4 py-3 flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Estás en</p>
                <h1 class="text-xl font-black text-indigo-600">{{ $table->name }}</h1>
            </div>
            <div class="bg-gray-100 rounded-full p-2">
                🍽️
            </div>
        </div>

        {{-- LISTA DE PRODUCTOS --}}
        <div class="p-4 grid grid-cols-1 gap-4">
            @foreach ($products as $product)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex">

                    {{-- Imagen Cuadrada --}}
                    <div class="w-28 h-28 bg-gray-100 flex-shrink-0">
                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
                    </div>

                    {{-- Info y Controles --}}
                    <div class="p-3 flex-1 flex flex-col justify-between">
                        <h3 class="font-bold text-gray-800 leading-tight">{{ $product->name }}</h3>

                        <div class="flex justify-end items-center mt-2">
                            @if (isset($cart[$product->id]))
                                {{-- Controles +/- --}}
                                <div class="flex items-center bg-indigo-50 rounded-lg p-1 border border-indigo-100">
                                    <button wire:click="removeFromCart({{ $product->id }})"
                                        class="w-8 h-8 flex items-center justify-center text-indigo-600 font-bold text-lg active:bg-indigo-200 rounded">-</button>
                                    <span
                                        class="w-8 text-center font-bold text-indigo-800">{{ $cart[$product->id] }}</span>
                                    <button wire:click="addToCart({{ $product->id }})"
                                        class="w-8 h-8 flex items-center justify-center text-indigo-600 font-bold text-lg active:bg-indigo-200 rounded">+</button>
                                </div>
                            @else
                                {{-- Botón Agregar --}}
                                <button wire:click="addToCart({{ $product->id }})"
                                    class="bg-gray-100 text-gray-700 hover:bg-indigo-600 hover:text-white font-bold py-2 px-4 rounded-lg text-sm transition-colors">
                                    Agregar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- BARRA FLOTANTE DEL CARRITO (Solo si hay items) --}}
        @if (count($cart) > 0)
            <div
                class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-30">
                <div class="max-w-md mx-auto">
                    <button wire:click="submitOrder" wire:loading.attr="disabled"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg flex justify-between items-center active:scale-95 transition transform">

                        <span class="bg-indigo-800 py-1 px-2 rounded text-xs">
                            {{ array_sum($cart) }} ítems
                        </span>

                        <span wire:loading.remove>Confirmar Pedido &rarr;</span>
                        <span wire:loading>Enviando... ⏳</span>

                        <span></span>
                    </button>
                </div>
            </div>
        @endif

    @endif
</div>
