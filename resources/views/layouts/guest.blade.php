<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Employee App') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Tailwind CSS & Vite -->
    @vite(['resources/js/app.ts'])
</head>
<body class="bg-slate-900 text-gray-100 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white text-gray-800 rounded-2xl shadow-2xl overflow-hidden p-8 border border-gray-100">
        <div class="flex flex-col items-center mb-6 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-800 via-rose-700 to-amber-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg mb-3">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">E-Employee System</h1>
            <p class="text-sm text-gray-500 mt-1">Sistem Absensi & Management Karyawan</p>
        </div>

        @yield('content')
    </div>
</body>
</html>
