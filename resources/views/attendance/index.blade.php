@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Presensi & Absensi Karyawan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Catat kehadiran harian, absensi istirahat, dan pantau riwayat bulan ini.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-200 text-right">
            <span class="text-xs text-gray-400 block font-medium uppercase">Hari & Tanggal</span>
            <span class="text-sm font-semibold text-gray-800">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Attendance Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Status & Geolocation Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between space-y-6">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Absensi Hari Ini</span>
                    @if($today)
                        <div class="flex items-center gap-1.5">
                            @if($today->jam_istirahat_mulai && !$today->jam_istirahat_selesai)
                                <span class="px-3 py-1 text-xs font-bold rounded-full uppercase bg-amber-100 text-amber-800 animate-pulse">
                                    <i class="fa-solid fa-mug-hot mr-1"></i> Sedang Istirahat
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold rounded-full uppercase {{ $today->status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $today->status }}
                                </span>
                            @endif
                            @if($today->status_istirahat === 'telat')
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase bg-rose-100 text-rose-800 border border-rose-200" title="Terlambat kembali dari istirahat (lewat 13:00)">
                                    Telat Istirahat
                                </span>
                            @endif
                        </div>
                    @else
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600 uppercase">
                            Belum Absen
                        </span>
                    @endif
                </div>

                <!-- 4 Box Time Status -->
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                    <div class="p-2 bg-white rounded-lg border border-gray-100">
                        <span class="text-[11px] text-gray-400 block font-medium">Jam Masuk</span>
                        <span class="text-sm sm:text-base font-bold font-mono text-emerald-700 block mt-0.5">
                            {{ $today && $today->jam_masuk ? $today->jam_masuk : '--:--:--' }}
                        </span>
                    </div>
                    <div class="p-2 bg-white rounded-lg border border-gray-100">
                        <span class="text-[11px] text-gray-400 block font-medium">Mulai Istirahat</span>
                        <span class="text-sm sm:text-base font-bold font-mono text-amber-700 block mt-0.5">
                            {{ $today && $today->jam_istirahat_mulai ? $today->jam_istirahat_mulai : '--:--:--' }}
                        </span>
                    </div>
                    <div class="p-2 bg-white rounded-lg border border-gray-100">
                        <span class="text-[11px] text-gray-400 block font-medium">Selesai Istirahat</span>
                        <span class="text-sm sm:text-base font-bold font-mono text-blue-700 block mt-0.5">
                            {{ $today && $today->jam_istirahat_selesai ? $today->jam_istirahat_selesai : '--:--:--' }}
                        </span>
                    </div>
                    <div class="p-2 bg-white rounded-lg border border-gray-100">
                        <span class="text-[11px] text-gray-400 block font-medium">Jam Keluar</span>
                        <span class="text-sm sm:text-base font-bold font-mono text-rose-700 block mt-0.5">
                            {{ $today && $today->jam_keluar ? $today->jam_keluar : '--:--:--' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <div id="geo-error" class="hidden text-xs text-rose-600 bg-rose-50 p-2.5 rounded-lg border border-rose-200">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Lokasi diperlukan untuk melakukan absen. Mohon izinkan akses lokasi di browser.
                </div>

                <!-- 4 Action Buttons Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- 1. Check-in Form -->
                    <form id="checkin-form" method="POST" action="{{ route('absensi.checkin') }}">
                        @csrf
                        <input type="hidden" name="latitude" id="checkin-lat">
                        <input type="hidden" name="longitude" id="checkin-lng">
                        <button type="button" onclick="handleAttendance('checkin-form', 'checkin-lat', 'checkin-lng')" 
                                @if($today) disabled @endif
                                class="w-full py-2.5 px-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-medium rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 text-xs sm:text-sm">
                            <i class="fa-solid fa-right-to-bracket"></i> Absen Masuk
                        </button>
                    </form>

                    <!-- 2. Mulai Istirahat Form -->
                    <form id="break-start-form" method="POST" action="{{ route('absensi.break.start') }}">
                        @csrf
                        <button type="submit" 
                                @if(!$today || $today->jam_istirahat_mulai || $today->jam_keluar) disabled @endif
                                class="w-full py-2.5 px-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-medium rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 text-xs sm:text-sm">
                            <i class="fa-solid fa-mug-hot"></i> Mulai Istirahat
                        </button>
                    </form>

                    <!-- 3. Selesai Istirahat Form -->
                    <form id="break-end-form" method="POST" action="{{ route('absensi.break.end') }}">
                        @csrf
                        <button type="submit" 
                                @if(!$today || !$today->jam_istirahat_mulai || $today->jam_istirahat_selesai || $today->jam_keluar) disabled @endif
                                class="w-full py-2.5 px-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-medium rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 text-xs sm:text-sm">
                            <i class="fa-solid fa-briefcase"></i> Selesai Istirahat
                        </button>
                    </form>

                    <!-- 4. Check-out Form -->
                    <form id="checkout-form" method="POST" action="{{ route('absensi.checkout') }}">
                        @csrf
                        <input type="hidden" name="latitude" id="checkout-lat">
                        <input type="hidden" name="longitude" id="checkout-lng">
                        <button type="button" onclick="handleAttendance('checkout-form', 'checkout-lat', 'checkout-lng')"
                                @if(!$today || ($today->jam_istirahat_mulai && !$today->jam_istirahat_selesai) || $today->jam_keluar) disabled @endif
                                class="w-full py-2.5 px-3 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-medium rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 text-xs sm:text-sm">
                            <i class="fa-solid fa-right-from-bracket"></i> Absen Keluar
                        </button>
                    </form>
                </div>

                <!-- Info Box Istirahat -->
                <div class="p-3 bg-amber-50/80 rounded-xl border border-amber-200 text-xs text-amber-900 flex items-start gap-2">
                    <i class="fa-solid fa-clock text-amber-600 mt-0.5 shrink-0"></i>
                    <div>
                        <span class="font-semibold block">Jadwal Istirahat: Pukul 12:00 s/d 13:00 WIB</span>
                        <span class="text-[11px] text-amber-800 block mt-0.5">
                            Pastikan melakukan <strong>Absen Selesai Istirahat</strong> sebelum pukul 13:00. Jika kembali lewat dari pukul 13:00, status kehadiran akan tercatat <strong>Terlambat</strong>.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information / Live Location Status Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-gray-900 text-base mb-2">Informasi Lokasi & Presensi</h3>
                <p class="text-sm text-gray-600">Pastikan perangkat Anda mendukung GPS / Fitur Lokasi. Koordinat lokasi Anda akan dicatat secara otomatis saat melakukan absen masuk dan keluar.</p>
            </div>
            
            <div class="mt-6 bg-slate-900 text-slate-100 p-4 rounded-xl font-mono text-xs space-y-2">
                <div class="flex justify-between items-center text-slate-400 border-b border-slate-800 pb-2">
                    <span>STATUS GEOLOCATION</span>
                    <span id="geo-status-badge" class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-amber-400">MEMUAT...</span>
                </div>
                <div class="flex justify-between">
                    <span>LATITUDE:</span>
                    <span id="current-lat">Detecting...</span>
                </div>
                <div class="flex justify-between">
                    <span>LONGITUDE:</span>
                    <span id="current-lng">Detecting...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Riwayat Presensi Bulan Ini</h2>
                <p class="text-xs text-gray-400 mt-0.5">Catatan riwayat kehadiran, waktu istirahat, dan durasi kerja.</p>
            </div>
            <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-medium">{{ count($riwayat) }} Catatan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-center">Jam Masuk</th>
                        <th class="px-4 py-3 text-center">Istirahat (Mulai - Selesai)</th>
                        <th class="px-4 py-3 text-center">Jam Keluar</th>
                        <th class="px-4 py-3 text-center">Durasi Kerja</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-center">Lokasi Maps</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}
                            </td>
                            <td class="px-4 py-4 text-center text-emerald-700 font-semibold font-mono whitespace-nowrap">
                                {{ $item->jam_masuk ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-center font-mono text-xs whitespace-nowrap">
                                @if($item->jam_istirahat_mulai || $item->jam_istirahat_selesai)
                                    <span class="{{ $item->status_istirahat === 'telat' ? 'text-rose-700 font-semibold' : 'text-gray-700' }}">
                                        {{ $item->jam_istirahat_mulai ?? '--:--' }} - {{ $item->jam_istirahat_selesai ?? '--:--' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center text-rose-700 font-semibold font-mono whitespace-nowrap">
                                {{ $item->jam_keluar ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-center text-xs font-medium text-gray-700 whitespace-nowrap">
                                {{ $item->durasi_kerja }}
                            </td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase {{ $item->status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-xs font-medium {{ $item->status === 'telat' ? 'text-rose-700' : 'text-emerald-700' }} whitespace-nowrap">
                                {{ $item->keterangan_ketepatan }}
                            </td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
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
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                                Belum ada riwayat presensi untuk bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('current-lat').innerText = position.coords.latitude;
                document.getElementById('current-lng').innerText = position.coords.longitude;
                const badge = document.getElementById('geo-status-badge');
                badge.innerText = 'AKTIF';
                badge.className = 'px-2 py-0.5 rounded text-[10px] bg-emerald-950 text-emerald-400';
            }, function(error) {
                const badge = document.getElementById('geo-status-badge');
                badge.innerText = 'GAGAL';
                badge.className = 'px-2 py-0.5 rounded text-[10px] bg-rose-950 text-rose-400';
            });
        }
    });

    function handleAttendance(formId, latInputId, lngInputId) {
        const errorEl = document.getElementById('geo-error');
        errorEl.classList.add('hidden');

        if (!navigator.geolocation) {
            errorEl.innerText = "Browser Anda tidak mendukung Geolocation.";
            errorEl.classList.remove('hidden');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById(latInputId).value = position.coords.latitude;
                document.getElementById(lngInputId).value = position.coords.longitude;
                document.getElementById(formId).submit();
            },
            function(error) {
                errorEl.innerText = "Gagal mengambil lokasi: " + error.message + ". Pastikan fitur GPS aktif.";
                errorEl.classList.remove('hidden');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
</script>
@endsection
