@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengajuan Izin Karyawan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar pengajuan izin dan status persetujuan dari atasan.</p>
        </div>
        <a href="{{ route('izin.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2.5 bg-rose-800 hover:bg-rose-900 text-white font-medium rounded-xl shadow-sm transition-colors gap-2">
            <i class="fa-solid fa-plus"></i> Ajukan Izin Baru
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Jenis Izin</th>
                        <th class="px-6 py-3">Tujuan</th>
                        <th class="px-6 py-3">Tanggal Mulai</th>
                        <th class="px-6 py-3">Jam / Durasi</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leaveRequests as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900 capitalize">
                                {{ str_replace('_', ' ', $item->jenis) }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $item->tujuan }}</td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                                @if($item->tanggal_selesai)
                                    <span class="text-xs text-gray-400 block">s.d. {{ \Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-mono">
                                @if($item->jam_mulai)
                                    {{ $item->jam_mulai }} - {{ $item->jam_selesai ?? '-' }}
                                @elseif($item->durasi_menit)
                                    {{ $item->durasi_menit }} Menit
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $item->keterangan }}</td>
                            <td class="px-6 py-4">
                                @if($item->status === 'approved')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 uppercase">Disetujui</span>
                                @elseif($item->status === 'rejected')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 uppercase">Ditolak</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 uppercase">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                                Belum ada pengajuan izin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
