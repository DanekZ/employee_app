@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Absensi Karyawan Keseluruhan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pantau riwayat dan catatan kehadiran seluruh karyawan secara real-time.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-200 text-right">
            <span class="text-xs text-gray-400 block font-medium uppercase">Total Data Terfilter</span>
            <span class="text-base font-bold text-rose-800">{{ $attendances->total() }} Catatan</span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('approval.absensi') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Filter Karyawan -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Pilih Karyawan</label>
                <select name="user_id" class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2.5">
                    <option value="">-- Semua Karyawan --</option>
                    @foreach($karyawanList as $karyawan)
                        <option value="{{ $karyawan->id }}" {{ request('user_id') == $karyawan->id ? 'selected' : '' }}>
                            {{ $karyawan->name }} ({{ $karyawan->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tanggal -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Filter Tanggal Spesifik</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                       class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2">
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Status Kehadiran</label>
                <select name="status" class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2.5">
                    <option value="">-- Semua Status --</option>
                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                    <option value="telat" {{ request('status') === 'telat' ? 'selected' : '' }}>Telat</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-rose-800 hover:bg-rose-900 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->anyFilled(['user_id', 'tanggal', 'bulan', 'status']))
                    <a href="{{ route('approval.absensi') }}" class="py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Attendance Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
            <h2 class="font-bold text-gray-900">Daftar Presensi Karyawan</h2>
            <span class="text-xs text-gray-500">Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} dari {{ $attendances->total() }} data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Nama Karyawan</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jam Masuk</th>
                        <th class="px-6 py-3">Jam Keluar</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Koordinat (Lat, Lng)</th>
                        <th class="px-6 py-3">Lokasi Maps</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-800 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $item->user->name ?? 'User Terhapus' }}</div>
                                        <div class="text-xs text-gray-400">{{ $item->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4 text-emerald-700 font-semibold font-mono">
                                {{ $item->jam_masuk ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-rose-700 font-semibold font-mono">
                                {{ $item->jam_keluar ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase {{ $item->status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-500">
                                {{ $item->latitude ?? '-' }}, {{ $item->longitude ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->latitude && $item->longitude)
                                    <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold border border-blue-200 transition-colors">
                                        <i class="fa-solid fa-map-location-dot text-blue-600"></i> Buka Maps
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                <i class="fa-solid fa-calendar-xmark text-4xl mb-2 text-gray-300 block"></i>
                                Tidak ada data presensi karyawan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
