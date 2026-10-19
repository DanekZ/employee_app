@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ 
    activeTab: 'payslips', 
    editSalaryModal: false, 
    selectedUser: null, 
    detailModal: false, 
    selectedSlip: null 
}">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-calculator text-rose-800"></i> Manajemen Penggajian & Rate
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Kelola gaji pokok karyawan, tarif per jam lembur/dinas, dan generate slip gaji berkala (Khusus Role Atasan).
            </p>
        </div>

        <!-- Filter Periode -->
        <form method="GET" action="{{ route('penggajian.index') }}" class="flex items-center gap-3">
            <label class="text-xs font-bold uppercase tracking-wider text-gray-400">Periode:</label>
            <input 
                type="month" 
                name="periode" 
                value="{{ $periode }}" 
                onchange="this.form.submit()"
                class="px-4 py-2 border border-gray-300 rounded-xl bg-gray-50 text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-rose-500"
            />
        </form>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 bg-white px-4 pt-2 rounded-t-2xl">
        <button 
            @click="activeTab = 'payslips'" 
            :class="activeTab === 'payslips' ? 'border-rose-800 text-rose-800 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="py-3 px-6 border-b-2 font-medium text-sm flex items-center gap-2 transition"
        >
            <i class="fa-solid fa-file-invoice-dollar"></i> Rekap Slip Gaji ({{ count($payslips) }})
        </button>
        <button 
            @click="activeTab = 'settings'" 
            :class="activeTab === 'settings' ? 'border-rose-800 text-rose-800 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="py-3 px-6 border-b-2 font-medium text-sm flex items-center gap-2 transition"
        >
            <i class="fa-solid fa-sliders"></i> Setting Rate Lembur & Dinas
        </button>
        <button 
            @click="activeTab = 'employees'" 
            :class="activeTab === 'employees' ? 'border-rose-800 text-rose-800 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="py-3 px-6 border-b-2 font-medium text-sm flex items-center gap-2 transition"
        >
            <i class="fa-solid fa-users-gear"></i> Gaji Pokok Karyawan ({{ count($employees) }})
        </button>
    </div>

    <!-- TAB 1: REKAP SLIP GAJI -->
    <div x-show="activeTab === 'payslips'" class="space-y-6">
        <!-- Action Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-4 bg-gray-100 p-4 rounded-xl border border-gray-200">
            <div class="text-sm font-medium text-gray-700">
                Status Slip Gaji Periode <span class="font-bold text-gray-900">{{ $periode }}</span>
            </div>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('penggajian.generate') }}" onsubmit="return confirm('Kalkulasi ulang slip gaji untuk periode ini?')">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <button type="submit" class="px-4 py-2 bg-rose-800 hover:bg-rose-900 text-white text-sm font-bold rounded-xl flex items-center gap-2 shadow-sm transition">
                        <i class="fa-solid fa-rotate"></i> Generate / Hitung Gaji
                    </button>
                </form>

                <form method="POST" action="{{ route('penggajian.publish') }}" onsubmit="return confirm('Terbitkan semua slip gaji untuk periode ini?')">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <button 
                        type="submit" 
                        {{ count($payslips) === 0 ? 'disabled' : '' }}
                        class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 disabled:opacity-50 text-white text-sm font-bold rounded-xl flex items-center gap-2 shadow-sm transition"
                    >
                        <i class="fa-solid fa-paper-plane"></i> Terbitkan Semua Slip
                    </button>
                </form>
            </div>
        </div>

        <!-- Table Payslips -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
            @if(count($payslips) === 0)
                <div class="p-12 text-center text-gray-500">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>
                    <p class="font-bold text-gray-700">Belum Ada Slip Gaji Dihasilkan</p>
                    <p class="text-xs text-gray-400 mt-1">Klik tombol "Generate / Hitung Gaji" di atas untuk membuat kalkulasi otomatis.</p>
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-500">
                            <th class="p-4">Karyawan</th>
                            <th class="p-4">Gaji Pokok</th>
                            <th class="p-4">Lembur</th>
                            <th class="p-4">Dinas</th>
                            <th class="p-4">Potongan</th>
                            <th class="p-4">Gaji Bersih</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($payslips as $slip)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ $slip->user->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $slip->user->email ?? '' }}</div>
                                </td>
                                <td class="p-4 font-semibold text-gray-800">Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</td>
                                <td class="p-4">
                                    <div class="text-emerald-600 font-medium">+Rp {{ number_format($slip->nominal_lembur, 0, ',', '.') }}</div>
                                    <div class="text-xs text-gray-400">{{ $slip->total_jam_lembur }} jam</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-emerald-600 font-medium">+Rp {{ number_format($slip->nominal_dinas, 0, ',', '.') }}</div>
                                    <div class="text-xs text-gray-400">{{ $slip->jumlah_hari_dinas }} tugas</div>
                                </td>
                                <td class="p-4 text-rose-600 font-medium">
                                    -Rp {{ number_format(($slip->potongan_absen ?? 0) + ($slip->potongan_izin ?? 0), 0, ',', '.') }}
                                </td>
                                <td class="p-4 font-extrabold text-rose-800 text-base">
                                    Rp {{ number_format($slip->total_gaji_bersih, 0, ',', '.') }}
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1 {{ $slip->status === 'terbit' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        <i class="fa-solid {{ $slip->status === 'terbit' ? 'fa-check-circle' : 'fa-clock' }}"></i>
                                        {{ $slip->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <button 
                                        @click="selectedSlip = {{ json_encode($slip) }}; detailModal = true"
                                        class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition"
                                    >
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- TAB 2: SETTING RATE GLOBAL -->
    <div x-show="activeTab === 'settings'" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm max-w-2xl">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
            <i class="fa-solid fa-coins text-rose-800"></i> Setting Nominal & Rate Per Jam/Hari
        </h3>

        <form method="POST" action="{{ route('penggajian.update-settings') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tarif Lembur (Per Jam)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold">Rp</span>
                    <input 
                        type="number" 
                        name="rate_lembur_per_jam" 
                        value="{{ old('rate_lembur_per_jam', $setting->rate_lembur_per_jam) }}"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-sm font-medium"
                        placeholder="Misal: 25000"
                        required
                    />
                </div>
                <p class="text-xs text-gray-400 mt-1">Dikalikan total jam lembur yang disetujui atasan.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tarif Dinas Luar (Per Jam)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold">Rp</span>
                    <input 
                        type="number" 
                        name="rate_dinas_per_jam" 
                        value="{{ old('rate_dinas_per_jam', $setting->rate_dinas_per_jam) }}"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-sm font-medium"
                        placeholder="Misal: 30000"
                        required
                    />
                </div>
                <p class="text-xs text-gray-400 mt-1">Tunjangan perjalanan dinas berdasarkan total jam dinas luar.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Uang Saku Dinas (Per Hari - Fallback)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold">Rp</span>
                    <input 
                        type="number" 
                        name="uang_saku_dinas_per_hari" 
                        value="{{ old('uang_saku_dinas_per_hari', $setting->uang_saku_dinas_per_hari) }}"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-sm font-medium"
                    />
                </div>
                <p class="text-xs text-gray-400 mt-1">Digunakan jika rate per jam bernilai 0.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Potongan Alpha (Per Hari)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold">Rp</span>
                        <input 
                            type="number" 
                            name="potongan_per_hari_alpha" 
                            value="{{ old('potongan_per_hari_alpha', $setting->potongan_per_hari_alpha) }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-sm font-medium"
                            required
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Potongan Izin (Per Hari)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold">Rp</span>
                        <input 
                            type="number" 
                            name="potongan_per_hari_izin" 
                            value="{{ old('potongan_per_hari_izin', $setting->potongan_per_hari_izin) }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-sm font-medium"
                            required
                        />
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 bg-rose-800 hover:bg-rose-900 text-white font-bold rounded-xl transition shadow-md mt-4">
                Simpan Pengaturan Rate
            </button>
        </form>
    </div>

    <!-- TAB 3: GAJI POKOK KARYAWAN -->
    <div x-show="activeTab === 'employees'" class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-500">
                    <th class="p-4">Nama Karyawan</th>
                    <th class="p-4">Role</th>
                    <th class="p-4">Gaji Pokok</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($employees as $emp)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-4">
                            <div class="font-bold text-gray-900">{{ $emp->name }}</div>
                            <div class="text-xs text-gray-400">{{ $emp->email }}</div>
                        </td>
                        <td class="p-4 font-medium text-gray-600 uppercase text-xs">{{ $emp->role }}</td>
                        <td class="p-4 font-bold text-gray-900">Rp {{ number_format($emp->gaji_pokok ?? 0, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            <button 
                                @click="selectedUser = {{ json_encode($emp) }}; editSalaryModal = true"
                                class="px-3 py-1.5 bg-rose-800 hover:bg-rose-900 text-white text-xs font-bold rounded-lg transition"
                            >
                                <i class="fa-solid fa-pen-to-square"></i> Edit Gaji Pokok
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Edit Gaji Pokok -->
    <div x-show="editSalaryModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
            <h3 class="text-lg font-bold text-gray-900">Edit Gaji Pokok <span x-text="selectedUser?.name"></span></h3>
            
            <form :action="'/penggajian/employee/' + selectedUser?.id + '/salary'" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Gaji Pokok (Rp)</label>
                    <input 
                        type="number" 
                        name="gaji_pokok" 
                        :value="selectedUser?.gaji_pokok ?? 0" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-base font-bold"
                        required
                    />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="editSalaryModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-xl text-sm">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-800 text-white font-bold rounded-xl text-sm hover:bg-rose-900">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Slip Gaji -->
    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="detailModal = false">
            <button @click="detailModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div class="text-center border-b pb-4">
                <h3 class="text-xl font-extrabold text-gray-900 tracking-wider">E-EMPLOYEE SYSTEM</h3>
                <p class="text-xs text-gray-500 font-medium">SLIP GAJI PERIODE <span x-text="selectedSlip?.periode"></span></p>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">Nama Karyawan:</span>
                    <span class="font-bold text-gray-900" x-text="selectedSlip?.user?.name"></span>
                </div>

                <div class="space-y-2">
                    <p class="font-bold text-xs uppercase text-emerald-700 tracking-wider">Pendapatan (+)</p>
                    <div class="flex justify-between text-xs text-gray-700 pl-2">
                        <span>Gaji Pokok</span>
                        <span class="font-semibold" x-text="'Rp ' + Number(selectedSlip?.gaji_pokok || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-700 pl-2">
                        <span>Lembur (<span x-text="selectedSlip?.total_jam_lembur"></span> Jam)</span>
                        <span class="font-semibold text-emerald-600" x-text="'+Rp ' + Number(selectedSlip?.nominal_lembur || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-700 pl-2">
                        <span>Dinas Luar (<span x-text="selectedSlip?.jumlah_hari_dinas"></span> Tugas)</span>
                        <span class="font-semibold text-emerald-600" x-text="'+Rp ' + Number(selectedSlip?.nominal_dinas || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <p class="font-bold text-xs uppercase text-rose-600 tracking-wider">Potongan (-)</p>
                    <div class="flex justify-between text-xs text-gray-700 pl-2">
                        <span>Absen Alpha (<span x-text="selectedSlip?.jumlah_hari_alpha"></span> Hari)</span>
                        <span class="font-semibold text-rose-500" x-text="'-Rp ' + Number(selectedSlip?.potongan_absen || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-700 pl-2">
                        <span>Izin (<span x-text="selectedSlip?.jumlah_hari_izin"></span> Hari)</span>
                        <span class="font-semibold text-rose-500" x-text="'-Rp ' + Number(selectedSlip?.potongan_izin || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <div class="flex justify-between items-center border-t border-b py-3 mt-3 bg-gray-50 px-3 rounded-xl">
                    <span class="font-bold text-gray-900 text-sm">TOTAL GAJI BERSIH</span>
                    <span class="font-black text-rose-800 text-lg" x-text="'Rp ' + Number(selectedSlip?.total_gaji_bersih || 0).toLocaleString('id-ID')"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
