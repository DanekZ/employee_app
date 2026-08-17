@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Akun & Profil</h1>
        <p class="text-sm text-gray-500 mt-0.5">Update informasi diri, email, dan kata sandi Anda.</p>
    </div>

    <!-- Navigation Sub-menu -->
    <div class="flex space-x-2 border-b border-gray-200">
        <a href="{{ route('profile.edit') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Profil Saya</a>
        <a href="{{ route('password.edit') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Ubah Kata Sandi</a>
        <a href="{{ route('appearance') }}" class="px-4 py-2 text-sm font-bold border-b-2 border-rose-800 text-rose-800">Tampilan</a>
    </div>

    <!-- Appearance Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200">
        <h2 class="text-lg font-bold text-gray-900 mb-2">Tema Tampilan Aplikasi</h2>
        <p class="text-sm text-gray-500 mb-6">Pilih preferensi tema antarmuka pengguna.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl border-2 border-rose-800 bg-rose-50/50 flex flex-col items-center justify-center text-center cursor-pointer">
                <i class="fa-solid fa-sun text-2xl text-amber-500 mb-2"></i>
                <span class="font-bold text-sm text-gray-900">Tema Terang (Light)</span>
                <span class="text-xs text-rose-700 font-semibold mt-1">Aktif</span>
            </div>
            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 flex flex-col items-center justify-center text-center opacity-60 cursor-not-allowed">
                <i class="fa-solid fa-moon text-2xl text-indigo-400 mb-2"></i>
                <span class="font-bold text-sm text-gray-800">Tema Gelap (Dark)</span>
                <span class="text-xs text-gray-400 mt-1">Segera Hadir</span>
            </div>
            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 flex flex-col items-center justify-center text-center opacity-60 cursor-not-allowed">
                <i class="fa-solid fa-desktop text-2xl text-gray-500 mb-2"></i>
                <span class="font-bold text-sm text-gray-800">Sistem Perangkat</span>
                <span class="text-xs text-gray-400 mt-1">Segera Hadir</span>
            </div>
        </div>
    </div>
</div>
@endsection
