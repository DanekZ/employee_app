@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: '{{ request('tab', 'izin') }}' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekapitulasi Pengajuan Karyawan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Histori lengkap pengajuan Izin, Lembur, dan Perjalanan Dinas (Pending, Disetujui, & Ditolak).</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('approval.rekap') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="hidden" name="tab" :value="activeTab">

            <!-- Filter Karyawan -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Pilih Karyawan</label>
                <select name="user_id" class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2.5">
                    <option value="">-- Semua Karyawan --</option>
                    @foreach($karyawanList as $karyawan)
                        <option value="{{ $karyawan->id }}" {{ $selectedUserId == $karyawan->id ? 'selected' : '' }}>
                            {{ $karyawan->name }} ({{ $karyawan->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Status Pengajuan</label>
                <select name="status" class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2.5">
                    <option value="">-- Semua Status --</option>
                    <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                    <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                    <option value="rejected" {{ $selectedStatus === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                </select>
            </div>

            <!-- Filter Bulan -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Filter Bulan</label>
                <input type="month" name="bulan" value="{{ $selectedBulan }}" 
                       class="w-full text-sm border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500 bg-gray-50/50 py-2">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-rose-800 hover:bg-rose-900 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->anyFilled(['user_id', 'status', 'bulan']))
                    <a href="{{ route('approval.rekap') }}" class="py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 flex space-x-4 bg-white px-6 pt-3 rounded-2xl shadow-sm">
        <button @click="activeTab = 'izin'" 
                :class="activeTab === 'izin' ? 'border-rose-800 text-rose-800 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-file-signature"></i>
            <span>Rekap Izin</span>
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-rose-100 text-rose-800 font-semibold">{{ $izin->total() }}</span>
        </button>
        <button @click="activeTab = 'lembur'" 
                :class="activeTab === 'lembur' ? 'border-rose-800 text-rose-800 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-business-time"></i>
            <span>Rekap Lembur</span>
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-rose-100 text-rose-800 font-semibold">{{ $lembur->total() }}</span>
        </button>
        <button @click="activeTab = 'dinas'" 
                :class="activeTab === 'dinas' ? 'border-rose-800 text-rose-800 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-building-user"></i>
            <span>Rekap Dinas Luar</span>
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-rose-100 text-rose-800 font-semibold">{{ $dinas->total() }}</span>
        </button>
    </div>

    <!-- Tab 1: Rekap Izin -->
    <div x-show="activeTab === 'izin'" class="space-y-4">
        <!-- Izin Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold text-emerald-600 uppercase">Disetujui (Approved)</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">{{ $totalIzinApproved }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold text-amber-600 uppercase">Menunggu (Pending)</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">{{ $totalIzinPending }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold text-rose-600 uppercase">Ditolak (Rejected)</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">{{ $totalIzinRejected }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3">Karyawan</th>
                            <th class="px-6 py-3">Jenis Izin</th>
                            <th class="px-6 py-3">Tujuan</th>
                            <th class="px-6 py-3">Tanggal / Waktu</th>
                            <th class="px-6 py-3">Keterangan</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($izin as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->user->name ?? 'User Terhapus' }}</td>
                                <td class="px-6 py-4 capitalize font-medium text-gray-700">{{ str_replace('_', ' ', $item->jenis) }}</td>
                                <td class="px-6 py-4 text-gray-800">{{ $item->tujuan }}</td>
                                <td class="px-6 py-4 text-xs">
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                                    @if($item->tanggal_selesai)
                                        - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $item->keterangan }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase {{ $item->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                                    Tidak ada data rekap pengajuan izin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($izin->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $izin->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Tab 2: Rekap Lembur -->
    <div x-show="activeTab === 'lembur'" class="space-y-4">
        <!-- Lembur Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold text-emerald-600 uppercase">Disetujui (Approved)</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">{{ $totalLemburApproved }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold text-amber-600 uppercase">Menunggu (Pending)</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">{{ $totalLemburPending }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold text-rose-600 uppercase">Ditolak (Rejected)</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">{{ $totalLemburRejected }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3">Karyawan</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Jam Lembur</th>
                            <th class="px-6 py-3">Lokasi</th>
                            <th class="px-6 py-3">Alasan</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lembur as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->user->name ?? 'User Terhapus' }}</td>
                                <td class="px-6 py-4 font-medium text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4 text-xs font-mono">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $item->lokasi_lembur }}</td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $item->alasan }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase {{ $item->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                                    Tidak ada data rekap pengajuan lembur.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($lembur->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $lembur->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Tab 3: Rekap Dinas Luar -->
    <div x-show="activeTab === 'dinas'" class="space-y-4">
        <!-- Dinas Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold text-emerald-600 uppercase">Disetujui (Approved)</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">{{ $totalDinasApproved }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold text-amber-600 uppercase">Menunggu (Pending)</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">{{ $totalDinasPending }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold text-rose-600 uppercase">Ditolak (Rejected)</span>
                    <span class="text-xl font-bold text-gray-900 block mt-1">{{ $totalDinasRejected }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3">Karyawan</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Alamat Tujuan</th>
                            <th class="px-6 py-3">Waktu & Transportasi</th>
                            <th class="px-6 py-3">Alasan</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dinas as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->user->name ?? 'User Terhapus' }}</td>
                                <td class="px-6 py-4 font-medium text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4 text-gray-800">{{ $item->tujuan_alamat }}</td>
                                <td class="px-6 py-4 text-xs">
                                    <span class="block font-mono">{{ $item->jam_keluar }} - {{ $item->jam_kembali }}</span>
                                    <span class="capitalize text-gray-400">{{ str_replace('_', ' ', $item->alat_transportasi) }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $item->alasan }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase {{ $item->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                                    Tidak ada data rekap pengajuan dinas luar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($dinas->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $dinas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
