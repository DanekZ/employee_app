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

    <!-- Tailwind CSS & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.ts'])

    <!-- Alpine.js for lightweight UI interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="flex flex-col min-h-screen font-sans antialiased text-gray-800 bg-gray-50" x-data="{ mobileMenuOpen: false, profileDropdownOpen: false }">
    
    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left Section: Logo & Desktop Links -->
                <div class="flex items-center space-x-8">
                    <div class="flex items-center flex-shrink-0 gap-2">
                        <div class="flex items-center justify-center w-24 h-24 text-lg font-bold text-white  rounded-xl">
                            <img src="{{ asset('logo.png') }}" alt="Logo" class="object-contain w-full h-full p-2">
                        </div>
                    </div> 

                    <!-- Desktop Menu Items with Dropdowns -->
                    <div class="hidden md:flex md:items-center md:space-x-3">
                        @auth
                            @if(auth()->user()->role === 'atasan')
                                <!-- Approval Link -->
                                <a href="{{ route('approval.index') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('approval.index') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="mr-2 fa-solid fa-list-check"></i> Approval Pengajuan
                                </a>

                                <!-- Penggajian & Rate Link -->
                                <a href="{{ route('penggajian.index') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('penggajian.*') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="mr-2 fa-solid fa-calculator"></i> Penggajian & Rate
                                </a>

                                <!-- Dropdown Rekap & Laporan -->
                                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                    <button @click="open = !open" 
                                            class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('approval.absensi', 'approval.rekap', 'approval.laporan') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <i class="mr-2 fa-solid fa-folder-open"></i> Rekap & Laporan
                                        <i class="ml-1.5 text-xs fa-solid fa-chevron-down transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute left-0 z-50 w-56 py-2 mt-2 bg-white border border-gray-100 shadow-xl rounded-xl">
                                        <a href="{{ route('approval.absensi') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-calendar-days text-gray-400"></i> Rekap Absensi
                                        </a>
                                        <a href="{{ route('approval.rekap') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-folder-closed text-gray-400"></i> Rekap Pengajuan
                                        </a>
                                        <a href="{{ route('approval.laporan') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-chart-pie text-gray-400"></i> Laporan Visualisasi
                                        </a>
                                    </div>
                                </div>

                            @elseif(auth()->user()->role === 'admin')
                                <!-- Absensi -->
                                <a href="{{ route('absensi.index') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('absensi.*') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="mr-2 fa-solid fa-calendar-check"></i> Absensi
                                </a>

                                <!-- Dropdown Pengajuan -->
                                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                    <button @click="open = !open" 
                                            class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('izin.*', 'lembur.*', 'dinas.*') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <i class="mr-2 fa-solid fa-paper-plane"></i> Pengajuan
                                        <i class="ml-1.5 text-xs fa-solid fa-chevron-down transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="open" 
                                         x-transition
                                         class="absolute left-0 z-50 w-48 py-2 mt-2 bg-white border border-gray-100 shadow-xl rounded-xl">
                                        <a href="{{ route('izin.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-file-signature text-gray-400"></i> Izin & Cuti
                                        </a>
                                        <a href="{{ route('lembur.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-business-time text-gray-400"></i> Lembur
                                        </a>
                                        <a href="{{ route('dinas.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-building-user text-gray-400"></i> Dinas Luar
                                        </a>
                                    </div>
                                </div>

                                <!-- Dropdown Rekap & Laporan -->
                                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                    <button @click="open = !open" 
                                            class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('approval.absensi', 'approval.rekap', 'approval.laporan') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <i class="mr-2 fa-solid fa-folder-open"></i> Rekap & Laporan
                                        <i class="ml-1.5 text-xs fa-solid fa-chevron-down transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="open" 
                                         x-transition
                                         class="absolute left-0 z-50 w-56 py-2 mt-2 bg-white border border-gray-100 shadow-xl rounded-xl">
                                        <a href="{{ route('approval.absensi') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-calendar-days text-gray-400"></i> Rekap Absensi
                                        </a>
                                        <a href="{{ route('approval.rekap') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-folder-closed text-gray-400"></i> Rekap Pengajuan
                                        </a>
                                        <a href="{{ route('approval.laporan') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-chart-pie text-gray-400"></i> Laporan Visualisasi
                                        </a>
                                    </div>
                                </div>

                                <!-- Slip Gaji Saya -->
                                <a href="{{ route('penggajian.my-payslips') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('penggajian.my-payslips') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="mr-2 fa-solid fa-file-invoice-dollar"></i> Slip Gaji Saya
                                </a>

                            @else
                                <!-- Karyawan View -->
                                <a href="{{ route('absensi.index') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('absensi.*') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="mr-2 fa-solid fa-calendar-check"></i> Absensi
                                </a>

                                <!-- Dropdown Pengajuan Karyawan -->
                                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                    <button @click="open = !open" 
                                            class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('izin.*', 'lembur.*', 'dinas.*') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                        <i class="mr-2 fa-solid fa-paper-plane"></i> Pengajuan Saya
                                        <i class="ml-1.5 text-xs fa-solid fa-chevron-down transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="open" 
                                         x-transition
                                         class="absolute left-0 z-50 w-48 py-2 mt-2 bg-white border border-gray-100 shadow-xl rounded-xl">
                                        <a href="{{ route('izin.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-file-signature text-gray-400"></i> Izin & Cuti
                                        </a>
                                        <a href="{{ route('lembur.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-business-time text-gray-400"></i> Lembur
                                        </a>
                                        <a href="{{ route('dinas.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-800">
                                            <i class="w-5 mr-2 fa-solid fa-building-user text-gray-400"></i> Dinas Luar
                                        </a>
                                    </div>
                                </div>

                                <!-- Slip Gaji Saya -->
                                <a href="{{ route('penggajian.my-payslips') }}" 
                                   class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('penggajian.my-payslips') ? 'bg-rose-50 text-rose-800 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                    <i class="mr-2 fa-solid fa-file-invoice-dollar"></i> Slip Gaji Saya
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Right Section: Profile & Actions -->
                <div class="hidden md:flex md:items-center md:space-x-4">
                    @auth
                        <!-- User Role Badge -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider {{ auth()->user()->role === 'atasan' ? 'bg-amber-100 text-amber-800' : (auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ auth()->user()->role }}
                        </span>

                        <!-- Profile Dropdown -->
                        <div class="relative" @click.away="profileDropdownOpen = false">
                            <button @click="profileDropdownOpen = !profileDropdownOpen" 
                                     class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900 focus:outline-none p-1.5 rounded-lg border border-transparent hover:border-gray-200">
                                <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white rounded-full bg-rose-800">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
                                <i class="text-xs text-gray-400 fa-solid fa-chevron-down"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="profileDropdownOpen"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 z-50 w-48 py-1 mt-2 bg-white border border-gray-100 shadow-lg rounded-xl">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="mr-2 text-gray-400 fa-regular fa-user"></i> Profil Saya
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-left text-rose-600 hover:bg-rose-50">
                                        <i class="mr-2 fa-solid fa-right-from-bracket text-rose-500"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>

                <!-- Mobile Hamburger Toggle -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-gray-600 rounded-lg hover:text-gray-900 focus:outline-none">
                        <i x-show="!mobileMenuOpen" class="text-xl fa-solid fa-bars"></i>
                        <i x-show="mobileMenuOpen" class="text-xl fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" class="bg-white border-b border-gray-200 md:hidden">
            <div class="px-3 pt-2 pb-3 space-y-1">
                @auth
                    @if(auth()->user()->role === 'atasan')
                        <a href="{{ route('approval.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="w-6 mr-2 fa-solid fa-list-check text-rose-800"></i> Approval Pengajuan
                        </a>
                        <a href="{{ route('penggajian.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="w-6 mr-2 fa-solid fa-calculator text-rose-800"></i> Penggajian & Rate
                        </a>
                        <div class="pt-2 border-t border-gray-100">
                            <p class="px-3 text-xs font-bold uppercase text-gray-400 mb-1">Rekap & Laporan</p>
                            <a href="{{ route('approval.absensi') }}" class="block px-3 py-1.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100">
                                <i class="w-6 mr-2 fa-solid fa-calendar-days text-gray-400"></i> Rekap Absensi Karyawan
                            </a>
                            <a href="{{ route('approval.rekap') }}" class="block px-3 py-1.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100">
                                <i class="w-6 mr-2 fa-solid fa-folder-closed text-gray-400"></i> Rekap Pengajuan
                            </a>
                            <a href="{{ route('approval.laporan') }}" class="block px-3 py-1.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100">
                                <i class="w-6 mr-2 fa-solid fa-chart-pie text-gray-400"></i> Laporan Visualisasi
                            </a>
                        </div>
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('absensi.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="w-6 mr-2 fa-solid fa-calendar-check text-rose-800"></i> Absensi
                        </a>
                        <div class="pt-2 border-t border-gray-100">
                            <p class="px-3 text-xs font-bold uppercase text-gray-400 mb-1">Pengajuan</p>
                            <a href="{{ route('izin.index') }}" class="block px-3 py-1.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100">
                                <i class="w-6 mr-2 fa-solid fa-file-signature text-gray-400"></i> Izin
                            </a>
                            <a href="{{ route('lembur.index') }}" class="block px-3 py-1.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100">
                                <i class="w-6 mr-2 fa-solid fa-business-time text-gray-400"></i> Lembur
                            </a>
                            <a href="{{ route('dinas.index') }}" class="block px-3 py-1.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100">
                                <i class="w-6 mr-2 fa-solid fa-building-user text-gray-400"></i> Dinas Luar
                            </a>
                        </div>
                        <a href="{{ route('penggajian.my-payslips') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="w-6 mr-2 fa-solid fa-file-invoice-dollar text-rose-800"></i> Slip Gaji Saya
                        </a>
                    @else
                        <a href="{{ route('absensi.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="w-6 mr-2 fa-solid fa-calendar-check text-rose-800"></i> Absensi
                        </a>
                        <div class="pt-2 border-t border-gray-100">
                            <p class="px-3 text-xs font-bold uppercase text-gray-400 mb-1">Pengajuan Saya</p>
                            <a href="{{ route('izin.index') }}" class="block px-3 py-1.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100">
                                <i class="w-6 mr-2 fa-solid fa-file-signature text-gray-400"></i> Izin
                            </a>
                            <a href="{{ route('lembur.index') }}" class="block px-3 py-1.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100">
                                <i class="w-6 mr-2 fa-solid fa-business-time text-gray-400"></i> Lembur
                            </a>
                            <a href="{{ route('dinas.index') }}" class="block px-3 py-1.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-100">
                                <i class="w-6 mr-2 fa-solid fa-building-user text-gray-400"></i> Dinas Luar
                            </a>
                        </div>
                        <a href="{{ route('penggajian.my-payslips') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="w-6 mr-2 fa-solid fa-file-invoice-dollar text-rose-800"></i> Slip Gaji Saya
                        </a>
                    @endif
                    <div class="pt-2 border-t border-gray-200">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="w-6 mr-2 fa-regular fa-user text-gray-400"></i> Profil Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 text-base font-medium text-left rounded-md text-rose-600 hover:bg-rose-50">
                                <i class="w-6 mr-2 fa-solid fa-right-from-bracket text-rose-500"></i> Keluar
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="flex-grow w-full px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="flex items-center justify-between px-4 py-3 mb-6 border shadow-sm bg-emerald-50 border-emerald-200 text-emerald-800 rounded-xl">
                <div class="flex items-center gap-3">
                    <i class="text-lg fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center justify-between px-4 py-3 mb-6 border shadow-sm bg-rose-50 border-rose-200 text-rose-800 rounded-xl">
                <div class="flex items-center gap-3">
                    <i class="text-lg fa-solid fa-circle-exclamation text-rose-600"></i>
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
    <footer class="py-4 text-xs text-center text-gray-500 bg-white border-t border-gray-200">
        &copy; {{ date('Y') }} Employee App System. All rights reserved.
    </footer>
</body>
</html>
