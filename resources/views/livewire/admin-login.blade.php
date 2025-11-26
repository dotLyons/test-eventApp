<div class="min-h-screen flex flex-col items-center justify-center bg-gray-900 px-4">

    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-sm text-center">
        <div class="text-6xl mb-4">🔐</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Acceso Restringido</h1>
        <p class="text-gray-500 text-sm mb-6">Ingresa el PIN de seguridad</p>

        <form wire:submit.prevent="login">
            {{-- q12Input del PIN (tipo password para que salgan puntitos) --}}
            <input wire:model="pin" type="password" inputmode="numeric" pattern="[0-9]*"
                class="w-full text-center text-4xl font-mono tracking-[0.5em] border-2 border-gray-300 rounded-xl py-3 mb-4 focus:border-indigo-600 focus:ring-0"
                placeholder="••••" maxlength="4" autofocus>

            @if ($error)
                <div class="text-red-500 font-bold mb-4 animate-pulse">
                    {{ $error }}
                </div>
            @endif

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition shadow-lg">
                ENTRAR
            </button>
        </form>
    </div>

    <p class="mt-8 text-gray-500 text-xs">Sistema de Gestión de Eventos v1.0</p>
</div>
