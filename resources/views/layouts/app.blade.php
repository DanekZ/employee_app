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

    <!-- Alpine.js for lightweight UI interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false, profileDropdownOpen: false }">
    
    <!-- Top Navigation Bar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left Section: Logo & Desktop Links -->
                <div class="flex items-center space-x-8">
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-800 via-rose-700 to-amber-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-gray-900">E-Employee</span>
                    </div>

                    <div class="hidden md:flex md:space-x-4">
                        @auth
                            @if(auth()->user()->role === 'atasan')
                                <a href="{{ route('approval.index') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('approval.*') ? 'bg-rose-50 text-rose-800' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="fa-solid fa-list-check mr-2"></i> Approval Pengajuan
                                </a>
                            @else
                                <a href="{{ route('absensi.index') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('absensi.*') ? 'bg-rose-50 text-rose-800' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="fa-solid fa-calendar-check mr-2"></i> Absensi
                                </a>
                                <a href="{{ route('izin.index') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('izin.*') ? 'bg-rose-50 text-rose-800' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="fa-solid fa-file-signature mr-2"></i> Izin
                                </a>
                                <a href="{{ route('lembur.index') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('lembur.*') ? 'bg-rose-50 text-rose-800' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="fa-solid fa-business-time mr-2"></i> Lembur
                                </a>
                                <a href="{{ route('dinas.index') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dinas.*') ? 'bg-rose-50 text-rose-800' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="fa-solid fa-building-user mr-2"></i> Dinas Luar
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Right Section: Profile & Actions -->
                <div class="hidden md:flex md:items-center md:space-x-4">
                    @auth
                        <!-- User Role Badge -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider {{ auth()->user()->role === 'atasan' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ auth()->user()->role }}
                        </span>

                        <!-- Profile Dropdown -->
                        <div class="relative" @click.away="profileDropdownOpen = false">
                            <button @click="profileDropdownOpen = !profileDropdownOpen" 
                                    class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 p-1.5 rounded-lg">
                                <div class="w-8 h-8 rounded-full bg-rose-800 text-white font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="profileDropdownOpen"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fa-regular fa-user mr-2 text-gray-400"></i> Profil Saya
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 font-medium">
                                        <i class="fa-solid fa-right-from-bracket mr-2 text-rose-500"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>

                <!-- Mobile Hamburger Toggle -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-gray-900 p-2 rounded-lg focus:outline-none">
                        <i x-show="!mobileMenuOpen" class="fa-solid fa-bars text-xl"></i>
                        <i x-show="mobileMenuOpen" class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" class="md:hidden border-b border-gray-200 bg-white">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @auth
                    @if(auth()->user()->role === 'atasan')
                        <a href="{{ route('approval.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-list-check mr-2"></i> Approval Pengajuan
                        </a>
                    @else
                        <a href="{{ route('absensi.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-calendar-check mr-2"></i> Absensi
                        </a>
                        <a href="{{ route('izin.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-file-signature mr-2"></i> Izin
                        </a>
                        <a href="{{ route('lembur.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-business-time mr-2"></i> Lembur
                        </a>
                        <a href="{{ route('dinas.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-building-user mr-2"></i> Dinas Luar
                        </a>
                    @endif
                    <div class="border-t border-gray-200 pt-2">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fa-regular fa-user mr-2"></i> Profil Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-rose-600 hover:bg-rose-50">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i> Keluar
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} Employee App System. All rights reserved.
    </footer>
</body>
</html>
