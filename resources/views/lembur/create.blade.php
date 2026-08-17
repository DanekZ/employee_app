@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('lembur.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Form Pengajuan Lembur</h1>
            <p class="text-sm text-gray-500">Isi detail waktu dan alasan lembur Anda di bawah ini.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('lembur.store') }}" class="space-y-6">
            @csrf

            <!-- Tanggal Lembur -->
            <div>
                <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lembur <span class="text-rose-500">*</span></label>
                <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('tanggal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Jam Mulai & Jam Selesai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jam_mulai" class="block text-sm font-semibold text-gray-700 mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    @error('jam_mulai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jam_selesai" class="block text-sm font-semibold text-gray-700 mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                    <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    @error('jam_selesai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Lokasi Lembur -->
            <div>
                <label for="lokasi_lembur" class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Lembur <span class="text-rose-500">*</span></label>
                <input type="text" id="lokasi_lembur" name="lokasi_lembur" value="{{ old('lokasi_lembur') }}" required placeholder="Contoh: Ruang Meeting Lantai 2 / WFH"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('lokasi_lembur') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Alasan Lembur -->
            <div>
                <label for="alasan" class="block text-sm font-semibold text-gray-700 mb-1">Alasan / Uraian Pekerjaan <span class="text-rose-500">*</span></label>
                <textarea id="alasan" name="alasan" rows="4" required placeholder="Jelaskan jenis pekerjaan atau target lembur..."
                          class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">{{ old('alasan') }}</textarea>
                @error('alasan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('lembur.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-rose-800 hover:bg-rose-900 text-white font-medium rounded-xl text-sm transition-colors shadow-sm">
                    <i class="fa-solid fa-paper-plane mr-1.5"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
