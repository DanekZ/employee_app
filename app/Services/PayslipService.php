<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\OfficeTrip;
use App\Models\OvertimeRequest;
use App\Models\Payslip;
use App\Models\SalarySetting;
use App\Models\User;
use Carbon\Carbon;

class PayslipService
{
    public function generate(User $user, string $periode): Payslip
    {
        $setting = SalarySetting::firstOrCreate([], [
            'rate_lembur_per_jam' => 0,
            'rate_dinas_per_jam' => 0,
            'potongan_per_hari_alpha' => 0,
            'potongan_per_hari_izin' => 0,
            'uang_saku_dinas_per_hari' => 0,
        ]);

        [$tahun, $bulan] = explode('-', $periode);

        // 1. Lembur (approved) — menambah gaji berdasarkan jam
        $totalJamLembur = OvertimeRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->sum(function ($item) {
                if ($item->jam_mulai && $item->jam_selesai) {
                    return Carbon::parse($item->jam_mulai)->diffInHours(Carbon::parse($item->jam_selesai));
                }

                return 0;
            });

        $nominalLembur = $totalJamLembur * ($setting->rate_lembur_per_jam ?? 0);

        // 2. Alpha — mengurangi gaji
        $jumlahHariAlpha = Attendance::where('user_id', $user->id)
            ->where('status', 'alpha')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->count();

        $potonganAbsen = $jumlahHariAlpha * ($setting->potongan_per_hari_alpha ?? 0);

        // 3. Dinas (approved) — menambah gaji (per jam atau per hari)
        $officeTrips = OfficeTrip::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $jumlahHariDinas = $officeTrips->count();
        $totalJamDinas = $officeTrips->sum(function ($item) {
            if ($item->jam_keluar && $item->jam_kembali) {
                return Carbon::parse($item->jam_keluar)->diffInHours(Carbon::parse($item->jam_kembali));
            }

            return 8; // default 8 jam jika tidak ada jam
        });

        if (($setting->rate_dinas_per_jam ?? 0) > 0) {
            $nominalDinas = $totalJamDinas * $setting->rate_dinas_per_jam;
        } else {
            $nominalDinas = $jumlahHariDinas * ($setting->uang_saku_dinas_per_hari ?? 0);
        }

        // 4. Izin (approved) — mengurangi gaji
        $jumlahHariIzin = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('tanggal_mulai', $tahun)
            ->whereMonth('tanggal_mulai', $bulan)
            ->count();

        $potonganIzin = $jumlahHariIzin * ($setting->potongan_per_hari_izin ?? 0);

        // 5. PPh 21 — default 0
        $pph21 = 0;

        // 6. Total Gaji Bersih
        $gajiPokok = $user->gaji_pokok ?? 0;
        $totalGajiBersih = $gajiPokok
            + $nominalLembur
            + $nominalDinas
            - $potonganAbsen
            - $potonganIzin
            - $pph21;

        return Payslip::updateOrCreate(
            ['user_id' => $user->id, 'periode' => $periode],
            [
                'gaji_pokok' => $gajiPokok,
                'total_jam_lembur' => $totalJamLembur,
                'nominal_lembur' => $nominalLembur,
                'jumlah_hari_alpha' => $jumlahHariAlpha,
                'potongan_absen' => $potonganAbsen,
                'jumlah_hari_dinas' => $jumlahHariDinas,
                'nominal_dinas' => $nominalDinas,
                'jumlah_hari_izin' => $jumlahHariIzin,
                'potongan_izin' => $potonganIzin,
                'pph21' => $pph21,
                'total_gaji_bersih' => max(0, $totalGajiBersih),
                'status' => 'draft',
            ]
        );
    }
}
