@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ selectedSlip: null }">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-rose-800"></i> Slip Gaji Saya
            </h2>
            <p class="text-sm text-gray-500 mt-1">Riwayat penerimaan gaji bulanan dan dokumen resmi slip gaji Anda.</p>
        </div>
    </div>

    <!-- Empty State -->
    @if(count($payslips) === 0)
        <div class="bg-white p-12 rounded-2xl border border-gray-200 text-center text-gray-500">
            <i class="fa-solid fa-receipt text-5xl mb-4 text-gray-300"></i>
            <h3 class="text-lg font-bold text-gray-800">Belum Ada Slip Gaji</h3>
            <p class="text-sm text-gray-400 mt-1">Slip gaji bulanan Anda akan muncul di sini setelah diterbitkan oleh Atasan.</p>
        </div>
    @else
        <!-- Payslips Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($payslips as $slip)
                <div class="bg-white p-6 rounded-2xl border border-gray-200 hover:shadow-md transition space-y-4 relative overflow-hidden flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <div>
                                <p class="text-xs font-bold uppercase text-gray-400">Periode</p>
                                <p class="text-lg font-extrabold text-gray-900">{{ $slip->periode }}</p>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase">
                                Terbit
                            </span>
                        </div>

                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Gaji Pokok:</span>
                                <span class="font-semibold text-gray-800">Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Lembur & Dinas:</span>
                                <span class="font-semibold text-emerald-600">
                                    +Rp {{ number_format(($slip->nominal_lembur ?? 0) + ($slip->nominal_dinas ?? 0), 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Potongan:</span>
                                <span class="font-semibold text-rose-600">
                                    -Rp {{ number_format(($slip->potongan_absen ?? 0) + ($slip->potongan_izin ?? 0), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-bold uppercase text-gray-400">Gaji Bersih</span>
                            <span class="text-lg font-black text-rose-800">Rp {{ number_format($slip->total_gaji_bersih, 0, ',', '.') }}</span>
                        </div>

                        <button 
                            @click="selectedSlip = {{ json_encode($slip) }}"
                            class="w-full py-2 bg-rose-800 hover:bg-rose-900 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition"
                        >
                            <i class="fa-solid fa-print"></i> Lihat & Cetak Slip
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Printable Slip Gaji Modal -->
    <div x-show="selectedSlip" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 print:p-0 print:bg-white print:static">
        <div class="bg-white rounded-2xl max-w-xl w-full p-8 space-y-6 shadow-2xl relative print:shadow-none print:w-full print:max-w-none" @click.away="selectedSlip = null">
            <!-- Close Button (Hidden when printing) -->
            <button @click="selectedSlip = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 print:hidden">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>

            <!-- Document Header / Kop -->
            <div class="text-center border-b border-gray-300 pb-5">
                <h1 class="text-2xl font-black text-gray-900 tracking-widest uppercase flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-gear text-rose-800"></i> E-EMPLOYEE SYSTEM
                </h1>
                <p class="text-xs text-gray-500 font-semibold tracking-wider mt-1">SLIP GAJI KARYAWAN RESMI</p>
                <div class="mt-2 text-sm font-bold bg-gray-100 py-1 px-4 rounded-full inline-block text-gray-800">
                    PERIODE: <span x-text="selectedSlip?.periode"></span>
                </div>
            </div>

            <!-- Employee Metadata -->
            <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">Nama Karyawan</p>
                    <p class="font-bold text-gray-900 text-base" x-text="selectedSlip?.user?.name"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">Email / Role</p>
                    <p class="font-semibold text-gray-700" x-text="(selectedSlip?.user?.email ?? '') + ' (' + (selectedSlip?.user?.role ?? '') + ')'"></p>
                </div>
            </div>

            <!-- Breakdown Details -->
            <div class="space-y-4 text-sm">
                <!-- Earnings -->
                <div>
                    <p class="font-bold text-xs uppercase text-emerald-700 tracking-wider mb-2 border-b pb-1">1. Rincian Pendapatan (+)</p>
                    <div class="space-y-1.5 pl-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Gaji Pokok</span>
                            <span class="font-semibold" x-text="'Rp ' + Number(selectedSlip?.gaji_pokok || 0).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Tunjangan Lembur (<span x-text="selectedSlip?.total_jam_lembur"></span> Jam)</span>
                            <span class="font-semibold text-emerald-600" x-text="'+Rp ' + Number(selectedSlip?.nominal_lembur || 0).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Uang Saku Dinas (<span x-text="selectedSlip?.jumlah_hari_dinas"></span> Perjalanan)</span>
                            <span class="font-semibold text-emerald-600" x-text="'+Rp ' + Number(selectedSlip?.nominal_dinas || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>

                <!-- Deductions -->
                <div>
                    <p class="font-bold text-xs uppercase text-rose-600 tracking-wider mb-2 border-b pb-1">2. Rincian Potongan (-)</p>
                    <div class="space-y-1.5 pl-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Potongan Absen Alpha (<span x-text="selectedSlip?.jumlah_hari_alpha"></span> Hari)</span>
                            <span class="font-semibold text-rose-500" x-text="'-Rp ' + Number(selectedSlip?.potongan_absen || 0).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Potongan Izin (<span x-text="selectedSlip?.jumlah_hari_izin"></span> Hari)</span>
                            <span class="font-semibold text-rose-500" x-text="'-Rp ' + Number(selectedSlip?.potongan_izin || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="flex justify-between items-center border-2 border-rose-800/20 bg-rose-50 p-4 rounded-xl mt-4">
                    <div>
                        <p class="text-xs font-bold uppercase text-rose-800">TOTAL GAJI BERSIH (TAKE HOME PAY)</p>
                    </div>
                    <span class="font-black text-2xl text-rose-800" x-text="'Rp ' + Number(selectedSlip?.total_gaji_bersih || 0).toLocaleString('id-ID')"></span>
                </div>
            </div>

            <!-- Action Bar (Hidden when printing) -->
            <div class="flex justify-end gap-3 pt-4 border-t print:hidden">
                <button @click="selectedSlip = null" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm">
                    Tutup
                </button>
                <button @click="window.print()" class="px-5 py-2 bg-rose-800 hover:bg-rose-900 text-white font-bold rounded-xl text-sm flex items-center gap-2 shadow-md">
                    <i class="fa-solid fa-print"></i> Cetak / Save PDF
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
