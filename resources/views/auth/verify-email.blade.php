@extends('layouts.guest')

@section('content')
<div class="mb-4 text-xs text-gray-600">
    Terima kasih telah mendaftar! Sebelum memulai, verifikasi alamat email Anda dengan menekan tautan yang baru saja kami kirimkan ke email Anda.
</div>

@if (session('status') == 'verification-link-sent')
    <div class="mb-4 text-xs font-semibold text-emerald-600 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
        Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
    </div>
@endif

<div class="space-y-3">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="w-full py-3 bg-gradient-to-r from-rose-800 to-rose-700 hover:from-rose-900 hover:to-rose-800 text-white font-bold rounded-xl shadow-md transition-all text-sm flex items-center justify-center gap-2">
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition-colors">
            Keluar (Logout)
        </button>
    </form>
</div>
@endsection
