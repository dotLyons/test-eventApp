<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Fiesta' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    {{-- 🔴 BOTÓN DE SALIR FLOTANTE --}}
    {{-- Solo se muestra si ya pasaron el Login del PIN --}}
    @if (session('admin_access'))
        <a href="{{ route('logout') }}" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"
            class="bg-red-600 text-white px-4 py-3 rounded-full shadow-xl font-bold text-xs flex items-center gap-2 hover:bg-red-700 transition-all border-2 border-white">
            🔒 Salir
        </a>
    @endif

    {{ $slot }}

</body>

</html>
