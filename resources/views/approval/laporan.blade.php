@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm 10mm 12mm 10mm;
        }
        body {
            background-color: white !important;
            color: #111827 !important;
            font-size: 11px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        nav, footer, .no-print {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
        .shadow-sm, .shadow-md, .shadow-lg {
            box-shadow: none !important;
        }
        .border {
            border-color: #cbd5e1 !important;
        }
        .bg-white {
            background-color: transparent !important;
        }
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 10.5px !important;
        }
        th {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            border: 1px solid #cbd5e1 !important;
            padding: 6px 8px !important;
            font-weight: 700 !important;
        }
        td {
            border: 1px solid #cbd5e1 !important;
            padding: 5px 8px !important;
        }
        thead {
            display: table-header-group !important;
        }
        tr {
            page-break-inside: avoid !important;
        }
        .page-break {
            page-break-before: always;
        }
    }
</style>

<div class="space-y-6">
    <!-- Printable Header / Kop Surat (Shown only when printing) -->
    <div class="hidden print-only border-b-2 border-gray-900 pb-4 mb-4">
        <div class="text-center">
            <h1 class="text-xl font-black uppercase tracking-wider text-gray-900">E-EMPLOYEE SYSTEM - SINARTAMJS</h1>
            <h2 class="text-base font-bold text-gray-800 mt-0.5">LAPORAN REKAPITULASI & DATA LENGKAP PRESENSI KARYAWAN</h2>
        </div>
        <div class="mt-3 pt-2 border-t border-gray-300 grid grid-cols-2 gap-2 text-xs text-gray-700">
            <div>
                <p><strong>Rentang Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
                <p><strong>Filter Karyawan:</strong> {{ $selectedUser ? $selectedUser->name . ' (' . $selectedUser->email . ')' : 'Semua Karyawan' }}</p>
            </div>
            <div class="text-right">
                <p><strong>Filter Status:</strong> {{ $selectedStatus ? ucfirst($selectedStatus) : 'Semua Status' }}</p>
                <p><strong>Dicetak Pada:</strong> {{ now()->translatedFormat('d F Y H:i') }} WIB | Oleh: {{ auth()->user()->name }}</p>
            </div>
        </div>

        <!-- Printable Mini KPI Summary Bar -->
        <div class="mt-3 grid grid-cols-4 gap-2 text-center text-xs">
            <div class="border border-gray-300 p-2 rounded bg-gray-50">
                <span class="block text-gray-500 text-[10px] uppercase font-semibold">Total Presensi</span>
                <span class="text-sm font-bold text-gray-900">{{ number_format($totalCount) }} Data</span>
            </div>
            <div class="border border-gray-300 p-2 rounded bg-gray-50">
                <span class="block text-gray-500 text-[10px] uppercase font-semibold">Tepat Waktu</span>
                <span class="text-sm font-bold text-emerald-700">{{ number_format($hadirCount) }} Data</span>
            </div>
            <div class="border border-gray-300 p-2 rounded bg-gray-50">
                <span class="block text-gray-500 text-[10px] uppercase font-semibold">Terlambat</span>
                <span class="text-sm font-bold text-rose-700">{{ number_format($telatCount) }} Data</span>
            </div>
            <div class="border border-gray-300 p-2 rounded bg-gray-50">
                <span class="block text-gray-500 text-[10px] uppercase font-semibold">Tingkat Kedisiplinan</span>
                <span class="text-sm font-bold text-amber-700">{{ $punctualityRate }}%</span>
            </div>
        </div>
    </div>

    <!-- Screen Header (Hidden on print) -->
    <div class="no-print flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Visualisasi Presensi Karyawan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Analisis tren kehadiran, statistik kedisiplinan, serta data riwayat absensi lengkap karyawan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('approval.absensi') }}" class="py-2.5 px-4 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold shadow-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-list-check text-gray-500"></i> Rekap Absensi
            </a>
            <button onclick="window.print()" class="py-2.5 px-4 bg-gray-900 hover:bg-black text-white rounded-xl text-sm font-semibold shadow-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-print text-rose-400"></i> Cetak Laporan / PDF
            </button>
        </div>
    </div>

    <!-- Filter Card (Hidden on print) -->
    <div class="no-print bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('approval.laporan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Filter Tanggal Mulai -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ $startDate }}" 
                       class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2">
            </div>

            <!-- Filter Tanggal Selesai -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ $endDate }}" 
                       class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2">
            </div>

            <!-- Filter Karyawan -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Karyawan</label>
                <select name="user_id" class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2.5">
                    <option value="">-- Semua Karyawan --</option>
                    @foreach($karyawanList as $karyawan)
                        <option value="{{ $karyawan->id }}" {{ $selectedUserId == $karyawan->id ? 'selected' : '' }}>
                            {{ $karyawan->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status & Action Buttons -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Status Kehadiran</label>
                <div class="flex items-center gap-2">
                    <select name="status" class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2.5">
                        <option value="">-- Semua Status --</option>
                        <option value="hadir" {{ $selectedStatus === 'hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                        <option value="telat" {{ $selectedStatus === 'telat' ? 'selected' : '' }}>Telat</option>
                    </select>
                    <button type="submit" class="py-2.5 px-4 bg-rose-800 hover:bg-rose-900 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors flex items-center justify-center gap-1.5 shrink-0" title="Terapkan Filter">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    <a href="{{ route('approval.laporan') }}" class="py-2.5 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center shrink-0" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>

        <!-- Quick Presets -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center gap-2 text-xs">
            <span class="text-gray-400 font-medium">Preset Periode:</span>
            <a href="{{ route('approval.laporan', ['tanggal_mulai' => now()->toDateString(), 'tanggal_selesai' => now()->toDateString(), 'user_id' => $selectedUserId, 'status' => $selectedStatus]) }}" 
               class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-rose-50 hover:text-rose-800 text-gray-600 font-medium transition-colors">
                Hari Ini
            </a>
            <a href="{{ route('approval.laporan', ['tanggal_mulai' => now()->subDays(7)->toDateString(), 'tanggal_selesai' => now()->toDateString(), 'user_id' => $selectedUserId, 'status' => $selectedStatus]) }}" 
               class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-rose-50 hover:text-rose-800 text-gray-600 font-medium transition-colors">
                7 Hari Terakhir
            </a>
            <a href="{{ route('approval.laporan', ['tanggal_mulai' => now()->startOfMonth()->toDateString(), 'tanggal_selesai' => now()->toDateString(), 'user_id' => $selectedUserId, 'status' => $selectedStatus]) }}" 
               class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-rose-50 hover:text-rose-800 text-gray-600 font-medium transition-colors">
                Bulan Ini
            </a>
            <a href="{{ route('approval.laporan', ['tanggal_mulai' => now()->subDays(30)->toDateString(), 'tanggal_selesai' => now()->toDateString(), 'user_id' => $selectedUserId, 'status' => $selectedStatus]) }}" 
               class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-rose-50 hover:text-rose-800 text-gray-600 font-medium transition-colors">
                30 Hari Terakhir
            </a>
            <a href="{{ route('approval.laporan', ['tanggal_mulai' => now()->subMonth()->startOfMonth()->toDateString(), 'tanggal_selesai' => now()->subMonth()->endOfMonth()->toDateString(), 'user_id' => $selectedUserId, 'status' => $selectedStatus]) }}" 
               class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-rose-50 hover:text-rose-800 text-gray-600 font-medium transition-colors">
                Bulan Lalu
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards (Hidden on print because print has dedicated header KPI bar) -->
    <div class="no-print grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Presensi -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Total Presensi</span>
                <span class="text-2xl font-bold text-gray-900 mt-1 block">{{ number_format($totalCount) }}</span>
                <span class="text-[11px] text-gray-400 mt-0.5 block">Record kehadiran</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
        </div>

        <!-- Hadir Tepat Waktu -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Tepat Waktu</span>
                <span class="text-2xl font-bold text-emerald-700 mt-1 block">{{ number_format($hadirCount) }}</span>
                <span class="text-[11px] text-emerald-600 mt-0.5 block font-medium">Hadir sebelum 08:00</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Terlambat (Telat)</span>
                <span class="text-2xl font-bold text-rose-700 mt-1 block">{{ number_format($telatCount) }}</span>
                <span class="text-[11px] text-rose-600 mt-0.5 block font-medium">Hadir lewat 08:00</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <!-- Rate Ketepatan Waktu -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Tingkat Kedisiplinan</span>
                <span class="text-2xl font-bold text-amber-700 mt-1 block">{{ $punctualityRate }}%</span>
                <span class="text-[11px] text-amber-600 mt-0.5 block font-medium">% Tepat waktu</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section (Hidden on print) -->
    <div class="no-print grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Daily Trend Bar Chart (Span 2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Tren Kehadiran Harian</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbandingan jumlah karyawan hadir tepat waktu vs terlambat per hari.</p>
                </div>
            </div>
            <div class="relative h-72">
                <canvas id="dailyTrendChart"></canvas>
            </div>
        </div>

        <!-- Ratio Donut Chart (Span 1 Column) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-gray-900 text-base mb-1">Rasio Ketepatan Waktu</h3>
                <p class="text-xs text-gray-400 mb-4">Proporsi persentase kehadiran global.</p>
            </div>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="ratioDonutChart"></canvas>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-around text-xs font-semibold">
                <div class="flex items-center gap-1.5 text-emerald-700">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    Tepat Waktu: {{ $hadirCount }}
                </div>
                <div class="flex items-center gap-1.5 text-rose-700">
                    <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                    Telat: {{ $telatCount }}
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Attendance Data Table (TABEL DATA ABSENSI LENGKAP KARYAWAN) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-gray-50/50">
            <div>
                <h2 class="font-bold text-gray-900 text-base">Data Absensi Lengkap Karyawan</h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Daftar seluruh catatan presensi detail untuk periode 
                    <strong class="text-gray-700">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong> s/d 
                    <strong class="text-gray-700">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                    {{ count($attendances) }} Catatan Ditemukan
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No.</th>
                        <th class="px-4 py-3">Tanggal & Hari</th>
                        <th class="px-5 py-3">Nama Karyawan</th>
                        <th class="px-4 py-3 text-center">Jam Masuk</th>
                        <th class="px-4 py-3 text-center">Istirahat</th>
                        <th class="px-4 py-3 text-center">Jam Keluar</th>
                        <th class="px-4 py-3 text-center">Durasi Kerja</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-center no-print">Lokasi</th>
                        <th class="px-4 py-3 print-only hidden">Koordinat GPS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $index => $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <!-- No -->
                            <td class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500">
                                {{ $loop->iteration }}
                            </td>

                            <!-- Tanggal & Hari -->
                            <td class="px-4 py-3.5 text-xs font-medium text-gray-900 whitespace-nowrap">
                                <div>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}</div>
                            </td>

                            <!-- Nama Karyawan -->
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="no-print w-7 h-7 rounded-full bg-rose-100 text-rose-800 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-xs">{{ $item->user->name ?? 'User Terhapus' }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $item->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Jam Masuk -->
                            <td class="px-4 py-3.5 text-center font-mono font-semibold text-xs text-emerald-700 whitespace-nowrap">
                                {{ $item->jam_masuk ?? '-' }}
                            </td>

                            <!-- Istirahat -->
                            <td class="px-4 py-3.5 text-center font-mono text-xs whitespace-nowrap">
                                @if($item->jam_istirahat_mulai || $item->jam_istirahat_selesai)
                                    <span class="{{ $item->status_istirahat === 'telat' ? 'text-rose-700 font-semibold' : 'text-gray-700' }}">
                                        {{ $item->jam_istirahat_mulai ?? '--:--' }} - {{ $item->jam_istirahat_selesai ?? '--:--' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Jam Keluar -->
                            <td class="px-4 py-3.5 text-center font-mono font-semibold text-xs text-rose-700 whitespace-nowrap">
                                {{ $item->jam_keluar ?? '-' }}
                            </td>

                            <!-- Durasi Kerja -->
                            <td class="px-4 py-3.5 text-center text-xs font-medium text-gray-700 whitespace-nowrap">
                                {{ $item->durasi_kerja }}
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @if($item->status === 'hadir')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Hadir
                                    </span>
                                @elseif($item->status === 'telat')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full uppercase bg-rose-100 text-rose-800 border border-rose-300">
                                        Telat
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full uppercase bg-gray-100 text-gray-800 border border-gray-300">
                                        {{ $item->status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Keterangan -->
                            <td class="px-4 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                                <span class="font-medium {{ $item->status === 'telat' ? 'text-rose-700' : 'text-emerald-700' }}">
                                    {{ $item->keterangan_ketepatan }}
                                </span>
                            </td>

                            <!-- Lokasi Google Maps (Screen only) -->
                            <td class="px-4 py-3.5 text-center no-print whitespace-nowrap">
                                @if($item->latitude && $item->longitude)
                                    <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-medium border border-blue-200 transition-colors">
                                        <i class="fa-solid fa-map-location-dot text-blue-600"></i> Maps
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Koordinat GPS (Print only) -->
                            <td class="px-4 py-3.5 text-xs font-mono text-gray-600 print-only hidden whitespace-nowrap">
                                {{ $item->latitude ?? '-' }}, {{ $item->longitude ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-clipboard-question text-4xl mb-3 text-gray-300 block"></i>
                                <span class="text-base font-semibold text-gray-700 block">Tidak ada catatan presensi ditemukan</span>
                                <span class="text-xs text-gray-500 mt-1 block">Silakan sesuaikan filter tanggal mulai, tanggal selesai, atau filter karyawan di atas.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Employee Discipline Ranking Table (Rekapitulasi Kedisiplinan Per Karyawan) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
            <div>
                <h2 class="font-bold text-gray-900 text-base">Rekapitulasi Kedisiplinan Per Karyawan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Ringkasan total kehadiran dan evaluasi persentase ketepatan waktu per individu.</p>
            </div>
            <span class="text-xs text-gray-500 font-medium">{{ count($employeeStats) }} Karyawan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3 text-center">Total Presensi</th>
                        <th class="px-6 py-3 text-center">Tepat Waktu</th>
                        <th class="px-6 py-3 text-center">Terlambat</th>
                        <th class="px-6 py-3 text-center">Persentase Kedisiplinan</th>
                        <th class="px-6 py-3 text-right">Status Evaluasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employeeStats as $stat)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="no-print w-8 h-8 rounded-full bg-rose-100 text-rose-800 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($stat['user']->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $stat['user']->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $stat['user']->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-800">{{ $stat['total'] }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-emerald-700">{{ $stat['hadir'] }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-rose-700">{{ $stat['telat'] }}</td>
                            <td class="px-6 py-4">
                                <div class="w-full max-w-xs mx-auto">
                                    <div class="flex justify-between items-center mb-1 text-xs">
                                        <span class="font-semibold text-gray-700">{{ $stat['rate'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden no-print">
                                        <div class="h-2 rounded-full {{ $stat['rate'] >= 95 ? 'bg-emerald-500' : ($stat['rate'] >= 80 ? 'bg-blue-500' : 'bg-rose-500') }}" 
                                             style="width: {{ $stat['rate'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $stat['badge_class'] }}">
                                    {{ $stat['rating'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-users-slash text-3xl mb-2 text-gray-300 block"></i>
                                Tidak ada data karyawan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Printable Signature Section (Tanda Tangan Pengesahan - Shown only on print) -->
    <div class="hidden print-only mt-10 pt-4">
        <div class="flex justify-between items-start text-xs">
            <div class="text-center w-64">
                <p class="text-gray-700">Diverifikasi & Dibuat Oleh,</p>
                <p class="font-bold text-gray-900 mt-0.5">Atasan Langsung / Tim HRD</p>
                <div class="h-16"></div>
                <p class="font-bold border-b border-gray-900 pb-0.5 inline-block min-w-[180px]">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-gray-500 mt-0.5">{{ auth()->user()->email }}</p>
            </div>
            <div class="text-center w-64">
                <p class="text-gray-700">Jakarta, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-gray-900 mt-0.5">Pimpinan / Direktur Operasional</p>
                <div class="h-16"></div>
                <p class="font-bold border-b border-gray-900 pb-0.5 inline-block min-w-[180px]">( ........................................ )</p>
                <p class="text-[10px] text-gray-500 mt-0.5">NIP: ....................................</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Daily Trend Stacked Bar Chart
        const dailyCtx = document.getElementById('dailyTrendChart');
        if (dailyCtx) {
            new Chart(dailyCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($dailyLabels),
                    datasets: [
                        {
                            label: 'Hadir Tepat Waktu',
                            data: @json($dailyHadir),
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                        },
                        {
                            label: 'Terlambat',
                            data: @json($dailyTelat),
                            backgroundColor: '#f43f5e',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { font: { family: 'Instrument Sans, sans-serif', size: 12 } }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        // Ratio Donut Chart
        const ratioCtx = document.getElementById('ratioDonutChart');
        if (ratioCtx) {
            new Chart(ratioCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Tepat Waktu', 'Terlambat'],
                    datasets: [{
                        data: [{{ $hadirCount }}, {{ $telatCount }}],
                        backgroundColor: ['#10b981', '#f43f5e'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
</script>
@endsection
