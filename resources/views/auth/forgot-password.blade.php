@extends('layouts.guest')

@section('content')
<div class="mb-4 text-xs text-gray-600">
    Lupa kata sandi Anda? Masukkan alamat email Anda di bawah ini untuk menerima tautan reset kata sandi.
</div>

@if (session('status'))
    <div class="mb-4 text-xs font-semibold text-emerald-600 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf

    <div>
        <label for="email" class="block text-xs font-bold uppercase text-gray-600 mb-1">Email Perusahaan</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
               placeholder="nama@perusahaan.com"
               class="w-full px-4 py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm">
        @error('email') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="w-full py-3 bg-gradient-to-r from-rose-800 to-rose-700 hover:from-rose-900 hover:to-rose-800 text-white font-bold rounded-xl shadow-md transition-all text-sm flex items-center justify-center gap-2">
        Kirim Tautan Reset
    </button>

    <div class="text-center pt-2 border-t border-gray-100">
        <a href="{{ route('login') }}" class="text-xs text-rose-800 font-bold hover:underline">Kembali ke Halaman Login</a>
    </div>
</form>
@endsection
