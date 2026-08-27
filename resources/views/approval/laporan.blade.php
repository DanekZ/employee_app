@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    @media print {
        body {
            background-color: white !important;
            color: black !important;
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
            border-color: #e5e7eb !important;
        }
        .page-break {
            page-break-before: always;
        }
    }
</style>

<div class="space-y-6">
    <!-- Printable Header / Kop Surat (Shown only when printing) -->
    <div class="hidden print-only text-center border-b-2 border-gray-900 pb-4 mb-6">
        <h1 class="text-2xl font-bold uppercase tracking-wider text-gray-900">E-EMPLOYEE SYSTEM</h1>
        <h2 class="text-lg font-semibold text-gray-700 mt-1">LAPORAN VISUALISASI REKAPITULASI PRESENSI KARYAWAN</h2>
        <p class="text-xs text-gray-500 mt-1">
            Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong>
            | Dicetak Pada: {{ now()->translatedFormat('d F Y H:i') }}
        </p>
    </div>

    <!-- Screen Header (Hidden on print) -->
    <div class="no-print flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Visualisasi Presensi Karyawan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Analisis tren kehadiran, ketepatan waktu, dan tingkat keterlambatan karyawan.</p>
        </div>
        <div class="flex items-center gap-3">
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

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-rose-800 hover:bg-rose-900 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-chart-line"></i> Tampilkan
                </button>
                <a href="{{ route('approval.laporan') }}" class="py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

        <!-- Quick Presets -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center gap-2 text-xs">
            <span class="text-gray-400 font-medium">Preset Periode:</span>
            <a href="{{ route('approval.laporan', ['tanggal_mulai' => now()->startOfMonth()->toDateString(), 'tanggal_selesai' => now()->toDateString(), 'user_id' => $selectedUserId]) }}" 
               class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-rose-50 hover:text-rose-800 text-gray-600 font-medium transition-colors">
                Bulan Ini
            </a>
            <a href="{{ route('approval.laporan', ['tanggal_mulai' => now()->subDays(30)->toDateString(), 'tanggal_selesai' => now()->toDateString(), 'user_id' => $selectedUserId]) }}" 
               class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-rose-50 hover:text-rose-800 text-gray-600 font-medium transition-colors">
                30 Hari Terakhir
            </a>
            <a href="{{ route('approval.laporan', ['tanggal_mulai' => now()->subMonth()->startOfMonth()->toDateString(), 'tanggal_selesai' => now()->subMonth()->endOfMonth()->toDateString(), 'user_id' => $selectedUserId]) }}" 
               class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-rose-50 hover:text-rose-800 text-gray-600 font-medium transition-colors">
                Bulan Lalu
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
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

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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

    <!-- Employee Discipline Ranking Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
            <div>
                <h2 class="font-bold text-gray-900">Rekapitulasi Kedisiplinan Per Karyawan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Evaluasi persentase ketepatan waktu hadir setiap karyawan.</p>
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
                                    <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-800 font-bold flex items-center justify-center text-xs">
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
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
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
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Daily Trend Stacked Bar Chart
        const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
        new Chart(dailyCtx, {
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

        // Ratio Donut Chart
        const ratioCtx = document.getElementById('ratioDonutChart').getContext('2d');
        new Chart(ratioCtx, {
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
    });
</script>
@endsection
