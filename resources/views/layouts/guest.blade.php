<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Employee App') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_sinarta.png') }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Tailwind CSS & Vite (CSS dimuat langsung di head) -->
    @vite(['resources/css/app.css', 'resources/js/app.ts'])

    <!-- Alpine.js for lightweight UI interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 font-sans antialiased text-gray-100 bg-slate-900">
    <div class="w-full max-w-md p-8 overflow-hidden text-gray-800 bg-white border border-gray-100 shadow-2xl rounded-2xl">
        <div class="flex flex-col items-center mb-6 text-center">
            <div class="flex items-center justify-center mb-3 text-2xl font-bold text-white w-30 h-30 rounded-2xl">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="object-contain w-full h-full p-2">
            </div>
            <p class="mt-1 text-sm text-gray-500">Sistem Absensi & Management Karyawan</p>
        </div>

        @yield('content')
    </div>
</body>
</html>
