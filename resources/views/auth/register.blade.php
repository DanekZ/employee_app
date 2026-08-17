@extends('layouts.guest')

@section('content')
<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <!-- Name -->
    <div>
        <label for="name" class="block text-xs font-bold uppercase text-gray-600 mb-1">Nama Lengkap</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-user text-sm"></i>
            </span>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                   placeholder="Nama Lengkap Karyawan"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm">
        </div>
        @error('name') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    <!-- Email Address -->
    <div>
        <label for="email" class="block text-xs font-bold uppercase text-gray-600 mb-1">Email Perusahaan</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-envelope text-sm"></i>
            </span>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   placeholder="nama@perusahaan.com"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm">
        </div>
        @error('email') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-xs font-bold uppercase text-gray-600 mb-1">Kata Sandi</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-lock text-sm"></i>
            </span>
            <input type="password" id="password" name="password" required
                   placeholder="Minimal 8 karakter"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm">
        </div>
        @error('password') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    <!-- Confirm Password -->
    <div>
        <label for="password_confirmation" class="block text-xs font-bold uppercase text-gray-600 mb-1">Konfirmasi Kata Sandi</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-lock text-sm"></i>
            </span>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   placeholder="Ulangi kata sandi"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm">
        </div>
        @error('password_confirmation') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full py-3 bg-gradient-to-r from-rose-800 to-rose-700 hover:from-rose-900 hover:to-rose-800 text-white font-bold rounded-xl shadow-md transition-all text-sm flex items-center justify-center gap-2 mt-2">
        <i class="fa-solid fa-user-plus"></i> Daftar Akun Baru
    </button>

    <div class="text-center pt-2 border-t border-gray-100">
        <span class="text-xs text-gray-500">Sudah punya akun?</span>
        <a href="{{ route('login') }}" class="text-xs text-rose-800 font-bold hover:underline ml-1">Masuk ke Akun</a>
    </div>
</form>
@endsection
