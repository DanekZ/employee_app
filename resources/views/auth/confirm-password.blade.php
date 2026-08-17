@extends('layouts.guest')

@section('content')
<div class="mb-4 text-xs text-gray-600">
    Ini adalah area terproteksi. Harap konfirmasi kata sandi Anda sebelum melanjutkan.
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
    @csrf

    <div>
        <label for="password" class="block text-xs font-bold uppercase text-gray-600 mb-1">Kata Sandi</label>
        <input type="password" id="password" name="password" required
               class="w-full px-4 py-2.5 rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm">
        @error('password') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="w-full py-3 bg-gradient-to-r from-rose-800 to-rose-700 hover:from-rose-900 hover:to-rose-800 text-white font-bold rounded-xl shadow-md transition-all text-sm flex items-center justify-center gap-2">
        Konfirmasi Kata Sandi
    </button>
</form>
@endsection
