<div class="flex flex-col md:flex-row min-h-[calc(100vh-80px)] bg-gray-100">

    {{-- 🟢 PANEL IZQUIERDO: LISTA DE MESAS CON DEUDA --}}
    <div class="w-full md:w-1/3 bg-white border-r border-gray-200 flex flex-col h-full">

        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800">📋 Mesas Abiertas</h2>
            <p class="text-sm text-gray-500">Selecciona una para cobrar</p>
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-2">
            @forelse ($tables as $table)
                @php
                    // Calculamos la deuda total de la mesa sumando sus órdenes impagas
                    $tableDebt = $table->orders->sum(fn($o) => $o->total);
                @endphp

                <button wire:click="selectTable({{ $table->id }})"
                    class="w-full text-left p-4 rounded-xl border transition-all duration-200 group
                    {{ $selectedTable && $selectedTable->id === $table->id
                        ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg'
                        : 'bg-white border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 text-gray-700' }}">

                    <div class="flex justify-between items-center">
                        <div>
                            <span class="block font-bold text-lg">{{ $table->name }}</span>
                            <span class="text-xs opacity-80">{{ $table->orders->count() }} órdenes pendientes</span>
                        </div>
                        <div class="text-right">
                            <span class="block font-mono font-bold text-xl">
                                ${{ number_format($tableDebt, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-gray-400">
                    <div class="text-4xl mb-2">✅</div>
                    <p>No hay cuentas abiertas.</p>
                </div>
            @endforelse
        </div>

        {{-- 🟢 NUEVO: RESUMEN DE CAJA (Footer del panel izquierdo - ALTO CONTRASTE) --}}
        <div class="p-5 bg-gray-100 border-t-2 border-gray-300 z-10">
            <h3 class="text-xs font-black uppercase tracking-wider text-gray-500 mb-3">
                📊 Arqueo de la Noche
            </h3>

            {{-- Fila Efectivo --}}
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-bold text-gray-700">💵 Efectivo:</span>
                <span class="font-mono font-bold text-green-700">
                    ${{ number_format($stats['cash'], 2, ',', '.') }}
                </span>
            </div>

            {{-- Fila Transferencia --}}
            <div class="flex justify-between items-center mb-3 border-b border-gray-300 pb-3">
                <span class="text-sm font-bold text-gray-700">📱 Transfer.:</span>
                <span class="font-mono font-bold text-blue-700">
                    ${{ number_format($stats['transfer'], 2, ',', '.') }}
                </span>
            </div>

            {{-- Fila Total --}}
            <div class="flex justify-between items-center text-xl">
                <span class="font-black text-black">TOTAL:</span>
                <span class="font-mono font-black text-black">
                    ${{ number_format($stats['total'], 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- 🔵 PANEL DERECHO: DETALLE Y COBRO --}}
    <div class="w-full md:w-2/3 p-4 md:p-8 flex flex-col">

        {{-- Mensajes Flash --}}
        @if (session()->has('message'))
            <div
                class="mb-4 p-4 bg-green-100 text-green-700 rounded-xl shadow-sm border border-green-200 flex items-center gap-3">
                <span class="text-2xl">💰</span>
                <span class="font-bold">{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-xl shadow-sm border border-red-200">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if ($selectedTable)
            @php
                // Cálculos en tiempo real para la vista
                $totalToPay = $selectedTable->orders->sum(fn($o) => $o->total);

                // Convertimos strings vacíos a 0 para evitar errores matemáticos
                $cash = floatval($cashAmount ?: 0);
                $transfer = floatval($transferAmount ?: 0);

                $totalPaid = $cash + $transfer;
                $difference = $totalPaid - $totalToPay;
            @endphp

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row h-full">

                {{-- Columna 1: Detalle de Consumo --}}
                <div class="w-full md:w-1/2 p-6 border-r border-gray-100 overflow-y-auto max-h-[500px]">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                        Consumo: {{ $selectedTable->name }}
                    </h3>

                    <div class="space-y-4">
                        @foreach ($selectedTable->orders as $order)
                            <div class="bg-gray-50 rounded-lg p-3 text-sm">
                                <div class="flex justify-between text-gray-500 text-xs mb-1">
                                    <span>Orden #{{ $order->id }}</span>
                                    <span>{{ $order->created_at->format('H:i') }}</span>
                                </div>
                                <ul class="space-y-1">
                                    @foreach ($order->items as $item)
                                        <li class="flex justify-between">
                                            <span>
                                                <span class="font-bold">{{ $item->quantity }}x</span>
                                                {{ $item->product_name_snapshot }}
                                            </span>
                                            <span class="font-mono text-gray-600">
                                                ${{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="text-right font-bold text-gray-800 mt-2 pt-1 border-t border-gray-200">
                                    Total Orden: ${{ number_format($order->total, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Columna 2: Caja de Cobro --}}
                <div class="w-full md:w-1/2 p-6 bg-gray-50 flex flex-col justify-between">
                    <div>
                        <h3 class="text-gray-500 font-bold uppercase tracking-wider text-xs mb-1">Total a Pagar</h3>
                        <div class="text-4xl font-black text-gray-900 mb-8">
                            ${{ number_format($totalToPay, 0, ',', '.') }}
                        </div>

                        <div class="space-y-6">
                            {{-- Input Efectivo --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">💵 Efectivo</label>
                                <div class="relative rounded-md shadow-sm">
                                    {{-- Símbolo $ centrado perfectamente --}}
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-xl font-bold">$</span>
                                    </div>

                                    {{-- Input con padding a la izquierda (pl-10) para no pisar el símbolo --}}
                                    <input wire:model.live="cashAmount" type="number" step="0.01"
                                        class="block w-full rounded-xl border-gray-300 pl-10 pr-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 text-xl font-mono text-gray-900 placeholder-gray-300"
                                        placeholder="0.00">
                                </div>
                            </div>

                            {{-- Input Transferencia --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">📱 Transferencia</label>
                                <div class="relative rounded-md shadow-sm">
                                    {{-- Símbolo $ centrado perfectamente --}}
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-xl font-bold">$</span>
                                    </div>

                                    {{-- Input con padding a la izquierda (pl-10) --}}
                                    <input wire:model.live="transferAmount" type="number" step="0.01"
                                        class="block w-full rounded-xl border-gray-300 pl-10 pr-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 text-xl font-mono text-gray-900 placeholder-gray-300"
                                        placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        {{-- Calculadora de Vuelto/Faltante --}}
                        <div
                            class="mt-8 p-4 rounded-xl {{ $difference >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <div class="flex justify-between text-sm font-bold mb-1">
                                <span>Pagado:</span>
                                <span>${{ number_format($totalPaid, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xl font-black">
                                <span>{{ $difference >= 0 ? 'Vuelto:' : 'Falta:' }}</span>
                                <span>${{ number_format(abs($difference), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Botón de Cierre --}}
                    <button wire:click="closeTable"
                        wire:confirm="¿Estás seguro de cerrar esta mesa? Se marcará como pagada."
                        @if ($difference < 0) disabled @endif
                        class="w-full mt-6 py-4 rounded-xl font-bold text-lg shadow-lg transition-all transform active:scale-95 flex justify-center items-center gap-2
                            {{ $difference >= 0
                                ? 'bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer'
                                : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}">
                        <span wire:loading.remove>✅ Cobrar y Cerrar Mesa</span>
                        <span wire:loading>Procesando...</span>
                    </button>
                </div>
            </div>
        @else
            {{-- Estado vacío (Ninguna mesa seleccionada) --}}
            <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-50">
                <div class="text-8xl mb-4">👈</div>
                <h2 class="text-2xl font-bold">Selecciona una mesa</h2>
                <p>para ver el detalle y cobrar.</p>
            </div>
        @endif
    </div>
</div>
