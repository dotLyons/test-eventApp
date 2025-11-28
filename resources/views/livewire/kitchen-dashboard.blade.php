<div class="p-6 bg-gray-100 min-h-screen" wire:poll.5s>
    {{-- wire:poll.5s hace que este componente se actualice cada 5 segundos --}}

    {{-- Lado Izquierdo: Título --}}
    <div class="flex items-center justify-between w-full md:w-auto">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2">
            👨‍🍳 Comandas
        </h1>

        {{-- Badge visible solo en móvil --}}
        <span class="md:hidden text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full animate-pulse">
            ● En vivo
        </span>
    </div>

    {{-- Lado Derecho: Badge Desktop + Botón Menú --}}
    <div class="flex items-center gap-3 self-end md:self-auto">
        {{-- Badge visible solo en escritorio --}}
        <span class="hidden md:inline-block text-sm bg-green-200 text-green-800 px-3 py-1 rounded-full animate-pulse">
            En vivo • Actualizando
        </span>

        {{-- EL NUEVO BOTÓN A PRODUCTOS --}}
        <a href="{{ route('products') }}"
            class="bg-white border border-gray-300 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 px-4 py-2 rounded-lg shadow-sm font-medium transition flex items-center gap-2 text-sm">
            🍔 Gestionar Menú
        </a>

        {{-- NUEVO: Botón MESAS --}}
        <a href="{{ route('tables') }}"
            class="bg-white border border-gray-300 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 px-3 py-2 rounded-lg shadow-sm font-medium transition text-sm">
            🪑 Mesas
        </a>

        {{-- Botón CAJA --}}
        <a href="{{ route('cashier') }}"
            class="bg-indigo-600 border border-indigo-600 text-white hover:bg-indigo-700 px-3 py-2 rounded-lg shadow-sm font-medium transition text-sm flex items-center gap-1">
            💰 Caja
        </a>
    </div>

    {{-- Mensajes de feedback --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Grilla de Pedidos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">

        @forelse ($orders as $order)
            <div class="bg-white rounded-xl shadow-lg border-l-4 border-indigo-500 overflow-hidden">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">
                                {{ $order->table->name }}
                            </h2>
                            <p class="text-xs text-gray-500">
                                Orden #{{ $order->id }} • {{ $order->created_at->format('H:i') }}
                            </p>
                        </div>
                        {{-- Cálculo de tiempo de espera (opcional) --}}
                        <span class="text-xs font-mono bg-gray-300 px-2 py-1 rounded">
                            {{ $order->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <div class="border-t border-b border-gray-100 py-3 my-3 space-y-2">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-700">
                                    {{ $item->product_name_snapshot }}
                                </span>
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded-full">
                                    x{{ $item->quantity }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Lógica visual dinámica según el estado --}}
                    @php
                        $btnColor = match ($order->status) {
                            'pending' => 'bg-gray-600 hover:bg-gray-700',
                            'in_preparation' => 'bg-blue-600 hover:bg-blue-700',
                            'ready' => 'bg-green-500 hover:bg-green-600 animate-pulse',
                            default => 'bg-gray-400',
                        };

                        $btnText = match ($order->status) {
                            'pending' => '👨‍🍳 Empezar a Cocinar',
                            'in_preparation' => '🔔 Avisar: Listo para llevar',
                            'ready' => '✅ Entregado / Cerrar',
                            default => 'Cerrar',
                        };
                    @endphp

                    <button wire:click="advanceStatus({{ $order->id }}, '{{ $order->status }}')"
                        wire:loading.attr="disabled"
                        class="w-full {{ $btnColor }} text-white font-bold py-2 px-4 rounded transition duration-200 flex justify-center items-center gap-2">
                        <span wire:loading.remove
                            wire:target="advanceStatus({{ $order->id }}, '{{ $order->status }}')">
                            {{ $btnText }}
                        </span>
                        <span wire:loading wire:target="advanceStatus({{ $order->id }}, '{{ $order->status }}')">
                            ⏳ Procesando...
                        </span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">☕</div>
                <p class="text-gray-500 text-xl">No hay pedidos pendientes.</p>
                <p class="text-gray-400 text-sm">Esperando a que los invitados escaneen el QR...</p>
            </div>
        @endforelse
    </div>
</div>
