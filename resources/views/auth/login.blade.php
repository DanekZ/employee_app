@extends('layouts.guest')

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    @if(session('status'))
        <div class="p-3 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <!-- Email Address -->
    <div>
        <label for="email" class="block text-xs font-bold uppercase text-gray-600 mb-1">Email Karyawan</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-envelope text-sm"></i>
            </span>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="nama@perusahaan.com"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm">
        </div>
        @error('email') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    <!-- Password -->
    <div>
        <div class="flex justify-between items-center mb-1">
            <label for="password" class="block text-xs font-bold uppercase text-gray-600">Kata Sandi</label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-rose-700 hover:underline font-medium">Lupa Kata Sandi?</a>
            @endif
        </div>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-lock text-sm"></i>
            </span>
            <input type="password" id="password" name="password" required
                   placeholder="••••••••"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm">
        </div>
        @error('password') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    <!-- Remember Me -->
    <div class="flex items-center">
        <input type="checkbox" id="remember_me" name="remember" class="rounded border-gray-300 text-rose-700 focus:ring-rose-500 h-4 w-4">
        <label for="remember_me" class="ml-2 block text-xs text-gray-600 font-medium">Ingat Saya</label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full py-3 bg-gradient-to-r from-rose-800 to-rose-700 hover:from-rose-900 hover:to-rose-800 text-white font-bold rounded-xl shadow-md transition-all text-sm flex items-center justify-center gap-2">
        <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
    </button>

    <div class="text-center pt-2 border-t border-gray-100">
        <span class="text-xs text-gray-500">Belum punya akun?</span>
        <a href="{{ route('register') }}" class="text-xs text-rose-800 font-bold hover:underline ml-1">Daftar Karyawan Baru</a>
    </div>
</form>
@endsection
