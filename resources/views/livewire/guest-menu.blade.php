<div class="min-h-screen bg-gray-50 pb-28"> {{-- pb-28 para dar espacio al botón flotante --}}

    {{-- 🚀 PANTALLA DE ÉXITO (MODAL) --}}
    @if ($isOrderPlaced)
        <div
            class="fixed inset-0 bg-orange-600 z-50 flex flex-col items-center justify-center text-white p-8 text-center animate-in fade-in duration-300">
            <div
                class="bg-white text-orange-600 rounded-full w-24 h-24 flex items-center justify-center text-6xl mb-6 shadow-xl animate-bounce">
                🚀
            </div>
            <h2 class="text-4xl font-black mb-2 tracking-tight">¡Pedido Recibido!</h2>
            <p class="text-xl opacity-90 mb-10 font-medium">La cocina ya está preparando lo tuyo.</p>

            <button wire:click="newOrder"
                class="bg-white text-orange-600 font-bold py-4 px-10 rounded-full shadow-2xl hover:scale-105 transition transform text-lg">
                Pedir algo más
            </button>
        </div>
    @else
        {{-- 🟠 HEADER: Marca Panetto Libertad --}}
        <div
            class="sticky top-0 z-40 bg-white/95 backdrop-blur-md shadow-sm border-b border-orange-100 px-4 py-3 flex justify-between items-center transition-all">

            {{-- Lado Izquierdo: Info Mesa --}}
            <div class="flex-1 pr-4">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">MESA ACTUAL</p>
                <h1 class="text-2xl font-black text-orange-600 truncate leading-none">{{ $table->name }}</h1>
            </div>

            {{-- Lado Derecho: Logo Panetto --}}
            <div class="flex items-center gap-3 flex-shrink-0">
                {{-- Contenedor del Logo --}}
                <div
                    class="w-14 h-14 rounded-full overflow-hidden border-2 border-orange-500 bg-white flex items-center justify-center shadow-sm">
                    {{-- ⚠️ ASEGÚRATE DE GUARDAR TU LOGO EN public/logo.png --}}
                    <img src="https://i.ibb.co/Lq7WZF6/PANETTO-GRANDES-EVENTOS-plateado.png" alt="Panetto" class="w-full h-full object-contain"
                        onerror="this.src='https://placehold.co/100x100/orange/white?text=Panetto'">
                </div>
            </div>
        </div>

        {{-- 📋 LISTA DE PRODUCTOS --}}
        <div class="p-4 grid grid-cols-1 gap-4 mt-2">
            @foreach ($products as $product)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-orange-100/50 overflow-hidden flex relative group">

                    {{-- Imagen Cuadrada --}}
                    <div class="w-32 h-32 bg-gray-100 flex-shrink-0 relative">
                        <img src="{{ $product->image_url }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @if (!$product->is_available)
                            <div
                                class="absolute inset-0 bg-gray-900/60 flex items-center justify-center backdrop-blur-[1px]">
                                <span
                                    class="text-white text-xs font-bold uppercase border border-white px-2 py-1 rounded">Agotado</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info y Controles --}}
                    <div class="p-3 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-gray-800 leading-tight text-lg">{{ $product->name }}</h3>
                            <p class="text-orange-600 font-black text-lg mt-1">
                                ${{ number_format($product->price, 2, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex justify-end items-center mt-2">
                            @if ($product->is_available)
                                @if (isset($cart[$product->id]))
                                    {{-- Controles +/- (Estilo Panetto) --}}
                                    <div
                                        class="flex items-center bg-orange-50 rounded-full p-1 border border-orange-200 shadow-sm">
                                        <button wire:click="removeFromCart({{ $product->id }})"
                                            class="w-8 h-8 flex items-center justify-center text-orange-600 font-bold text-xl hover:bg-orange-100 rounded-full transition">-</button>
                                        <span
                                            class="w-8 text-center font-bold text-gray-800 text-lg">{{ $cart[$product->id] }}</span>
                                        <button wire:click="addToCart({{ $product->id }})"
                                            class="w-8 h-8 flex items-center justify-center bg-orange-500 text-white font-bold text-xl hover:bg-orange-600 rounded-full shadow-sm transition">+</button>
                                    </div>
                                @else
                                    {{-- Botón Agregar (Naranja) --}}
                                    <button wire:click="addToCart({{ $product->id }})"
                                        class="bg-orange-50 text-orange-700 border border-orange-200 hover:bg-orange-500 hover:text-white hover:border-orange-500 font-bold py-2 px-5 rounded-full text-sm transition-all shadow-sm">
                                        Agregar
                                    </button>
                                @endif
                            @else
                                <span class="text-gray-400 text-sm font-medium italic">No disponible</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 🛒 BARRA FLOTANTE DEL CARRITO --}}
        @if (count($cart) > 0)
            <div
                class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-orange-100 z-50 animate-in slide-in-from-bottom-4 duration-300">
                <div class="max-w-md mx-auto">
                    <button wire:click="submitOrder" wire:loading.attr="disabled"
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-orange-500/30 flex justify-between items-center active:scale-95 transition transform">

                        {{-- IZQUIERDA: Contador --}}
                        <div class="flex items-center gap-2">
                            <span class="bg-white text-orange-600 py-1 px-3 rounded-lg text-sm font-black shadow-sm">
                                {{ array_sum($cart) }}
                            </span>
                            <span class="text-orange-100 text-sm font-medium">ítems</span>
                        </div>

                        {{-- CENTRO: Texto --}}
                        <span wire:loading.remove
                            class="text-base uppercase tracking-widest font-black">Confirmar</span>
                        <span wire:loading class="text-base font-medium">Enviando... ⏳</span>

                        {{-- DERECHA: Total --}}
                        <div class="font-mono text-xl font-bold">
                            ${{ number_format($totalAmount, 2, ',', '.') }}
                        </div>
                    </button>
                </div>
            </div>
        @endif

    @endif
</div>
