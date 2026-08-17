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
        <a href="{{ route('profile.edit') }}" class="px-4 py-2 text-sm font-bold border-b-2 border-rose-800 text-rose-800">Profil Saya</a>
        <a href="{{ route('password.edit') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Ubah Kata Sandi</a>
        <a href="{{ route('appearance') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Tampilan</a>
    </div>

    <!-- Profile Form Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Profil</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Role Badge (Read only) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Peran / Hak Akses</label>
                <input type="text" value="{{ ucfirst(auth()->user()->role) }}" disabled
                       class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-500 text-sm py-2.5 cursor-not-allowed">
            </div>

            <div class="pt-3 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-rose-800 hover:bg-rose-900 text-white font-medium rounded-xl text-sm transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
