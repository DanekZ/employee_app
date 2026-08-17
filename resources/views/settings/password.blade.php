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
        <a href="{{ route('password.edit') }}" class="px-4 py-2 text-sm font-bold border-b-2 border-rose-800 text-rose-800">Ubah Kata Sandi</a>
        <a href="{{ route('appearance') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Tampilan</a>
    </div>

    <!-- Password Form Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Ubah Kata Sandi</h2>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi Saat Ini</label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('current_password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi Baru</label>
                <input type="password" id="password" name="password" required
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('password_confirmation') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-3 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-rose-800 hover:bg-rose-900 text-white font-medium rounded-xl text-sm transition-colors shadow-sm">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
