@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengajuan Perjalanan Dinas Luar</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftar perjalanan dinas luar kantor dan status persetujuannya.</p>
        </div>
        <a href="{{ route('dinas.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2.5 bg-rose-800 hover:bg-rose-900 text-white font-medium rounded-xl shadow-sm transition-colors gap-2">
            <i class="fa-solid fa-plus"></i> Ajukan Dinas Baru
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Tujuan & Alamat</th>
                        <th class="px-6 py-3">Waktu Dinas</th>
                        <th class="px-6 py-3">Transportasi</th>
                        <th class="px-6 py-3">Alasan</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($officeTrips as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $item->tujuan_alamat }}</td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-800">
                                {{ $item->jam_keluar }} - {{ $item->jam_kembali }}
                            </td>
                            <td class="px-6 py-4 capitalize text-xs">
                                <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 font-medium">
                                    {{ str_replace('_', ' ', $item->alat_transportasi) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $item->alasan }}</td>
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
                                Belum ada pengajuan perjalanan dinas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
