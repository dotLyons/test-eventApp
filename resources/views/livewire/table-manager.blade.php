<div class="min-h-screen bg-gray-100 p-4 md:p-8">

    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">🪑 Gestión de Mesas</h1>
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline font-medium">
                &larr; Volver a la Cocina
            </a>
        </div>

        {{-- Formulario de Creación --}}
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Nueva Mesa</h2>

            <form wire:submit.prevent="save" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Mesa</label>
                    <input wire:model="name" type="text" placeholder="Ej: Mesa 15"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition">
                    + Agregar
                </button>
            </form>

            @if (session()->has('message'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                    class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-bold">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        {{-- Lista de Mesas y QRs --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($tables as $table)
                @php
                    // Generamos la URL completa que usará el cliente
                    // Si estás en local usará tu IP, si estás en Render usará el dominio real.
                    $menuUrl = route('guest.menu', ['uuid' => $table->uuid]);

                    // URL de la API para generar la imagen del QR
                    $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($menuUrl);
                @endphp

                <div
                    class="bg-white rounded-xl shadow overflow-hidden border border-gray-200 flex flex-col items-center p-4">

                    <h3 class="font-bold text-xl text-gray-800 mb-2">{{ $table->name }}</h3>

                    {{-- El Código QR --}}
                    <div class="border-4 border-gray-100 rounded-lg p-2 mb-4">
                        <img src="{{ $qrImage }}" alt="QR Mesa" class="w-32 h-32">
                    </div>

                    {{-- Enlace de texto (para copiar) --}}
                    <div class="w-full bg-gray-50 rounded p-2 text-center mb-4">
                        <p class="text-xs text-gray-500 truncate select-all font-mono">
                            {{ $menuUrl }}
                        </p>
                        <a href="{{ $menuUrl }}" target="_blank"
                            class="text-xs text-indigo-600 hover:underline block mt-1">
                            Probar enlace &rarr;
                        </a>
                    </div>

                    {{-- Botón Eliminar --}}
                    <button wire:click="delete({{ $table->id }})"
                        wire:confirm="¿Borrar {{ $table->name }}? Se perderán los pedidos históricos asociados."
                        class="w-full py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-sm font-bold transition">
                        🗑️ Eliminar
                    </button>

                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-400">
                    No hay mesas creadas.
                </div>
            @endforelse
        </div>
    </div>
</div>
