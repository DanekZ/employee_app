@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('izin.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Form Pengajuan Izin</h1>
            <p class="text-sm text-gray-500">Lengkapi detail pengajuan izin Anda di bawah ini.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('izin.store') }}" class="space-y-6">
            @csrf

            <!-- Jenis Izin -->
            <div>
                <label for="jenis" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Izin <span class="text-rose-500">*</span></label>
                <select id="jenis" name="jenis" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    <option value="tidak_masuk" {{ old('jenis') == 'tidak_masuk' ? 'selected' : '' }}>Tidak Masuk Kerja</option>
                    <option value="terlambat" {{ old('jenis') == 'terlambat' ? 'selected' : '' }}>Terlambat Hadir</option>
                    <option value="pulang_awal" {{ old('jenis') == 'pulang_awal' ? 'selected' : '' }}>Pulang Lebih Awal</option>
                    <option value="keluar_kantor" {{ old('jenis') == 'keluar_kantor' ? 'selected' : '' }}>Keluar Jam Kantor</option>
                </select>
                @error('jenis') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tujuan -->
            <div>
                <label for="tujuan" class="block text-sm font-semibold text-gray-700 mb-1">Tujuan / Keperluan <span class="text-rose-500">*</span></label>
                <input type="text" id="tujuan" name="tujuan" value="{{ old('tujuan') }}" required placeholder="Contoh: Urusan keluarga mendesak"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('tujuan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    @error('tanggal_mulai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Selesai (Opsional)</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    @error('tanggal_selesai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Jam & Durasi -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="jam_mulai" class="block text-sm font-semibold text-gray-700 mb-1">Jam Mulai</label>
                    <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    @error('jam_mulai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jam_selesai" class="block text-sm font-semibold text-gray-700 mb-1">Jam Selesai</label>
                    <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    @error('jam_selesai') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="durasi_menit" class="block text-sm font-semibold text-gray-700 mb-1">Durasi (Menit)</label>
                    <input type="number" id="durasi_menit" name="durasi_menit" value="{{ old('durasi_menit') }}" min="1" placeholder="e.g. 60"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    @error('durasi_menit') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan Tambahan <span class="text-rose-500">*</span></label>
                <textarea id="keterangan" name="keterangan" rows="4" required placeholder="Jelaskan alasan detail pengajuan izin..."
                          class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">{{ old('keterangan') }}</textarea>
                @error('keterangan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('izin.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition-colors">
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
