@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('dinas.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Form Pengajuan Perjalanan Dinas</h1>
            <p class="text-sm text-gray-500">Lengkapi informasi perjalanan dinas Anda di bawah ini.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('dinas.store') }}" class="space-y-6"
              x-data="{
                  alatTransportasi: '{{ old('alat_transportasi', 'kendaraan_dinas') }}',
                  peranKendaraan: '{{ old('peran_kendaraan', 'bawa_sendiri') }}'
              }">
            @csrf

            <!-- Tanggal Dinas -->
            <div>
                <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Perjalanan Dinas <span class="text-rose-500">*</span></label>
                <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('tanggal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tujuan Alamat -->
            <div>
                <label for="tujuan_alamat" class="block text-sm font-semibold text-gray-700 mb-1">Tujuan / Alamat Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" id="tujuan_alamat" name="tujuan_alamat" value="{{ old('tujuan_alamat') }}" required placeholder="Contoh: Kantor Cabang Surabaya / PT Klien"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                @error('tujuan_alamat') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Jam Keluar & Jam Kembali -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jam_keluar" class="block text-sm font-semibold text-gray-700 mb-1">Jam Berangkat / Keluar <span class="text-rose-500">*</span></label>
                    <input type="time" id="jam_keluar" name="jam_keluar" value="{{ old('jam_keluar') }}" required
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    @error('jam_keluar') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jam_kembali" class="block text-sm font-semibold text-gray-700 mb-1">Jam Estimasi Kembali <span class="text-rose-500">*</span></label>
                    <input type="time" id="jam_kembali" name="jam_kembali" value="{{ old('jam_kembali') }}" required
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    @error('jam_kembali') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Alat Transportasi -->
            <div>
                <label for="alat_transportasi" class="block text-sm font-semibold text-gray-700 mb-1">Alat Transportasi <span class="text-rose-500">*</span></label>
                <select id="alat_transportasi" name="alat_transportasi" x-model="alatTransportasi" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">
                    <option value="kendaraan_dinas">Kendaraan Dinas</option>
                    <option value="kendaraan_pribadi">Kendaraan Pribadi</option>
                    <option value="transportasi_umum">Transportasi Umum</option>
                </select>
                @error('alat_transportasi') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Opsi Tambahan jika Kendaraan Pribadi -->
            <div x-show="alatTransportasi === 'kendaraan_pribadi'" x-cloak class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Peran Kendaraan Pribadi <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex items-start p-3 border rounded-xl cursor-pointer transition-all"
                               :class="peranKendaraan === 'bawa_sendiri' ? 'bg-white border-amber-500 ring-2 ring-amber-500/20' : 'bg-white/70 border-gray-200 hover:bg-white'">
                            <input type="radio" name="peran_kendaraan" value="bawa_sendiri" x-model="peranKendaraan" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <div class="ml-3">
                                <span class="block text-sm font-semibold text-gray-800">Bawa Motor/Mobil Sendiri</span>
                                <span class="block text-xs text-emerald-600 font-medium mt-0.5"><i class="fa-solid fa-gas-pump mr-1"></i>Mendapatkan Uang Bensin</span>
                            </div>
                        </label>
                        <label class="relative flex items-start p-3 border rounded-xl cursor-pointer transition-all"
                               :class="peranKendaraan === 'menumpang' ? 'bg-white border-amber-500 ring-2 ring-amber-500/20' : 'bg-white/70 border-gray-200 hover:bg-white'">
                            <input type="radio" name="peran_kendaraan" value="menumpang" x-model="peranKendaraan" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <div class="ml-3">
                                <span class="block text-sm font-semibold text-gray-800">Menumpang Rekan Kerja</span>
                                <span class="block text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-user-group mr-1"></i>Hanya Benefit Dinas (Tanpa Bensin)</span>
                            </div>
                        </label>
                    </div>
                    @error('peran_kendaraan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Dropdown Menumpang Siapa -->
                <div x-show="peranKendaraan === 'menumpang'" x-cloak class="pt-2">
                    <label for="menumpang_user_id" class="block text-sm font-semibold text-gray-700 mb-1">
                        Menumpang dengan Siapa? <span class="text-rose-500">*</span>
                    </label>
                    <select id="menumpang_user_id" name="menumpang_user_id"
                            :required="alatTransportasi === 'kendaraan_pribadi' && peranKendaraan === 'menumpang'"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5 bg-white">
                        <option value="">-- Pilih Rekan Kerja --</option>
                        @foreach($rekanKerja as $rekan)
                            <option value="{{ $rekan->id }}" {{ old('menumpang_user_id') == $rekan->id ? 'selected' : '' }}>
                                {{ $rekan->name }} ({{ $rekan->nip ?? 'Karyawan' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih nama rekan kerja pemilik kendaraan yang Anda tumpangi.</p>
                    @error('menumpang_user_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Alasan Dinas -->
            <div>
                <label for="alasan" class="block text-sm font-semibold text-gray-700 mb-1">Alasan / Maksud Perjalanan Dinas <span class="text-rose-500">*</span></label>
                <textarea id="alasan" name="alasan" rows="4" required placeholder="Jelaskan agenda kerja perjalanan dinas..."
                          class="w-full rounded-xl border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm py-2.5">{{ old('alasan') }}</textarea>
                @error('alasan') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('dinas.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition-colors">
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
