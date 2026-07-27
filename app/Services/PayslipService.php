<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payslip;
use App\Models\SalarySetting;
use App\Models\OvertimeRequest;
use App\Models\OfficeTrip;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use Carbon\Carbon;

class PayslipService
{
    public function generate(User $user, string $periode): Payslip
    {
        $setting = SalarySetting::first();

        [$tahun, $bulan] = explode('-', $periode);

        // 1. Lembur (approved) — menambah gaji
        $totalJamLembur = OvertimeRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->sum(function ($item) {
                return Carbon::parse($item->jam_mulai)->diffInHours(Carbon::parse($item->jam_selesai));
            });

        $nominalLembur = $totalJamLembur * $setting->rate_lembur_per_jam;

        // 2. Alpha — mengurangi gaji
        $jumlahHariAlpha = Attendance::where('user_id', $user->id)
            ->where('status', 'alpha')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->count();

        $potonganAbsen = $jumlahHariAlpha * $setting->potongan_per_hari_alpha;

        // 3. Dinas (approved) — menambah gaji (uang saku per hari)
        $jumlahHariDinas = OfficeTrip::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('tanggal_mulai', $tahun)
            ->whereMonth('tanggal_mulai', $bulan)
            ->get()
            ->sum(function ($item) {
                return Carbon::parse($item->tanggal_mulai)->diffInDays($item->tanggal_selesai) + 1;
            });

        $nominalDinas = $jumlahHariDinas * $setting->uang_saku_dinas_per_hari;

        // 4. Izin (approved) — mengurangi gaji, rate terpisah dari alpha
        $jumlahHariIzin = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->count();

        $potonganIzin = $jumlahHariIzin * $setting->potongan_per_hari_izin;

        // 5. PPh 21 — belum dipakai untuk saat ini
        $pph21 = 0;

        // 6. Total
        $totalGajiBersih = $user->gaji_pokok
            + $nominalLembur
            + $nominalDinas
            - $potonganAbsen
            - $potonganIzin
            - $pph21;

        return Payslip::updateOrCreate(
            ['user_id' => $user->id, 'periode' => $periode],
            [
                'gaji_pokok' => $user->gaji_pokok,
                'total_jam_lembur' => $totalJamLembur,
                'nominal_lembur' => $nominalLembur,
                'jumlah_hari_alpha' => $jumlahHariAlpha,
                'potongan_absen' => $potonganAbsen,
                'jumlah_hari_dinas' => $jumlahHariDinas,
                'nominal_dinas' => $nominalDinas,
                'jumlah_hari_izin' => $jumlahHariIzin,
                'potongan_izin' => $potonganIzin,
                'pph21' => $pph21,
                'total_gaji_bersih' => $totalGajiBersih,
                'status' => 'draft',
            ]
        );
    }
}