@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'izin' }">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Approval Atasan</h1>
        <p class="text-sm text-gray-500 mt-0.5">Kelola dan proses pengajuan izin, lembur, dan dinas luar dari anggota tim/bawahan Anda.</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 flex space-x-4 bg-white px-6 pt-3 rounded-2xl shadow-sm">
        <button @click="activeTab = 'izin'" 
                :class="activeTab === 'izin' ? 'border-rose-800 text-rose-800 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-file-signature"></i>
            <span>Izin Karyawan</span>
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-rose-100 text-rose-800 font-semibold">{{ count($izin) }}</span>
        </button>
        <button @click="activeTab = 'lembur'" 
                :class="activeTab === 'lembur' ? 'border-rose-800 text-rose-800 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-business-time"></i>
            <span>Lembur</span>
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-rose-100 text-rose-800 font-semibold">{{ count($lembur) }}</span>
        </button>
        <button @click="activeTab = 'dinas'" 
                :class="activeTab === 'dinas' ? 'border-rose-800 text-rose-800 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-building-user"></i>
            <span>Dinas Luar</span>
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-rose-100 text-rose-800 font-semibold">{{ count($dinas) }}</span>
        </button>
    </div>

    <!-- Tab 1: Izin -->
    <div x-show="activeTab === 'izin'" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="font-bold text-gray-900">Pengajuan Izin Pending</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Jenis Izin</th>
                        <th class="px-6 py-3">Tujuan</th>
                        <th class="px-6 py-3">Tanggal / Waktu</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($izin as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->user->name ?? 'Karyawan' }}</td>
                            <td class="px-6 py-4 capitalize font-medium text-gray-700">{{ str_replace('_', ' ', $item->jenis) }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $item->tujuan }}</td>
                            <td class="px-6 py-4 text-xs">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                                @if($item->tanggal_selesai)
                                    - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $item->keterangan }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('izin.approve', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('izin.reject', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center gap-1">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-circle-check text-3xl mb-2 text-emerald-400 block"></i>
                                Tidak ada pengajuan izin yang memerlukan persetujuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 2: Lembur -->
    <div x-show="activeTab === 'lembur'" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="font-bold text-gray-900">Pengajuan Lembur Pending</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jam Lembur</th>
                        <th class="px-6 py-3">Lokasi</th>
                        <th class="px-6 py-3">Alasan</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lembur as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->user->name ?? 'Karyawan' }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 text-xs font-mono">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $item->lokasi_lembur }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $item->alasan }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('lembur.approve', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('lembur.reject', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center gap-1">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-circle-check text-3xl mb-2 text-emerald-400 block"></i>
                                Tidak ada pengajuan lembur yang memerlukan persetujuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 3: Dinas -->
    <div x-show="activeTab === 'dinas'" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="font-bold text-gray-900">Pengajuan Perjalanan Dinas Pending</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Tujuan</th>
                        <th class="px-6 py-3">Waktu & Transportasi</th>
                        <th class="px-6 py-3">Alasan</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dinas as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->user->name ?? 'Karyawan' }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $item->tujuan_alamat }}</td>
                            <td class="px-6 py-4 text-xs">
                                <span class="block font-mono">{{ $item->jam_keluar }} - {{ $item->jam_kembali }}</span>
                                <span class="capitalize text-gray-400">{{ str_replace('_', ' ', $item->alat_transportasi) }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $item->alasan }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('dinas.approve', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('dinas.reject', $item->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center gap-1">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-circle-check text-3xl mb-2 text-emerald-400 block"></i>
                                Tidak ada pengajuan dinas yang memerlukan persetujuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
