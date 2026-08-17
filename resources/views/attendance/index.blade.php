@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Presensi & Absensi Karyawan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Catat kehadiran Anda harian dan pantau riwayat bulan ini.</p>
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
                        <span class="px-3 py-1 text-xs font-bold rounded-full uppercase {{ $today->status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $today->status }}
                        </span>
                    @else
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600 uppercase">
                            Belum Absen
                        </span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <span class="text-xs text-gray-400 block">Jam Masuk</span>
                        <span class="text-lg font-bold text-gray-800">{{ $today && $today->jam_masuk ? $today->jam_masuk : '--:--:--' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Jam Keluar</span>
                        <span class="text-lg font-bold text-gray-800">{{ $today && $today->jam_keluar ? $today->jam_keluar : '--:--:--' }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div>
                <div id="geo-error" class="hidden mb-3 text-xs text-rose-600 bg-rose-50 p-2.5 rounded-lg border border-rose-200">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Lokasi diperlukan untuk melakukan absen. Mohon izinkan akses lokasi di browser.
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Check-in Form -->
                    <form id="checkin-form" method="POST" action="{{ route('absensi.checkin') }}">
                        @csrf
                        <input type="hidden" name="latitude" id="checkin-lat">
                        <input type="hidden" name="longitude" id="checkin-lng">
                        <button type="button" onclick="handleAttendance('checkin-form', 'checkin-lat', 'checkin-lng')" 
                                @if($today) disabled @endif
                                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-right-to-bracket"></i> Absen Masuk
                        </button>
                    </form>

                    <!-- Check-out Form -->
                    <form id="checkout-form" method="POST" action="{{ route('absensi.checkout') }}">
                        @csrf
                        <input type="hidden" name="latitude" id="checkout-lat">
                        <input type="hidden" name="longitude" id="checkout-lng">
                        <button type="button" onclick="handleAttendance('checkout-form', 'checkout-lat', 'checkout-lng')"
                                @if(!$today || $today->jam_keluar) disabled @endif
                                class="w-full py-3 px-4 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-right-from-bracket"></i> Absen Keluar
                        </button>
                    </form>
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
            <h2 class="text-lg font-bold text-gray-900">Riwayat Presensi Bulan Ini</h2>
            <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-medium">{{ count($riwayat) }} Catatan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jam Masuk</th>
                        <th class="px-6 py-3">Jam Keluar</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Koordinat (Lat, Lng)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                            <td class="px-6 py-4 text-emerald-700 font-semibold">{{ $item->jam_masuk ?? '-' }}</td>
                            <td class="px-6 py-4 text-rose-700 font-semibold">{{ $item->jam_keluar ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase {{ $item->status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-500">
                                {{ $item->latitude ?? '-' }}, {{ $item->longitude ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
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
