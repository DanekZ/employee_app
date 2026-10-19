<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Models\SalarySetting;
use App\Models\User;
use App\Services\PayslipService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    /**
     * Halaman Utama Penggajian (Khusus Atasan) - Blade View
     */
    public function index(Request $request): View
    {
        $periode = $request->input('periode', now()->format('Y-m'));

        $setting = SalarySetting::firstOrCreate([], [
            'rate_lembur_per_jam' => 0,
            'rate_dinas_per_jam' => 0,
            'potongan_per_hari_alpha' => 0,
            'potongan_per_hari_izin' => 0,
            'uang_saku_dinas_per_hari' => 0,
        ]);

        $employees = User::whereIn('role', ['karyawan', 'atasan'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'gaji_pokok']);

        $payslips = Payslip::with('user')
            ->where('periode', $periode)
            ->get();

        return view('payroll.index', [
            'periode' => $periode,
            'setting' => $setting,
            'employees' => $employees,
            'payslips' => $payslips,
        ]);
    }

    /**
     * Generate Slip Gaji Masal per Periode (Khusus Atasan)
     */
    public function generate(Request $request, PayslipService $payslipService)
    {
        $request->validate([
            'periode' => 'required|date_format:Y-m',
        ]);

        $periode = $request->periode;
        $employees = User::whereIn('role', ['karyawan', 'atasan'])->get();

        foreach ($employees as $employee) {
            $payslipService->generate($employee, $periode);
        }

        return redirect()->back()->with('success', "Slip gaji periode {$periode} berhasil dikalkulasi.");
    }

    /**
     * Publikasikan Slip Gaji Periode Ini (Khusus Atasan)
     */
    public function publish(Request $request)
    {
        $request->validate([
            'periode' => 'required|date_format:Y-m',
        ]);

        Payslip::where('periode', $request->periode)->update([
            'status' => 'terbit',
            'diterbitkan_at' => now(),
        ]);

        return redirect()->back()->with('success', "Slip gaji periode {$request->periode} telah berhasil diterbitkan.");
    }

    /**
     * Update Setting Rate Gaji Global (Khusus Atasan)
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'rate_lembur_per_jam' => 'required|numeric|min:0',
            'rate_dinas_per_jam' => 'required|numeric|min:0',
            'potongan_per_hari_alpha' => 'required|numeric|min:0',
            'potongan_per_hari_izin' => 'required|numeric|min:0',
            'uang_saku_dinas_per_hari' => 'nullable|numeric|min:0',
        ]);

        $setting = SalarySetting::firstOrCreate([]);
        $setting->update($validated);

        return redirect()->back()->with('success', 'Pengaturan rate penggajian berhasil diperbarui.');
    }

    /**
     * Update Gaji Pokok Per Karyawan (Khusus Atasan)
     */
    public function updateEmployeeSalary(Request $request, User $user)
    {
        $validated = $request->validate([
            'gaji_pokok' => 'required|numeric|min:0',
        ]);

        $user->update([
            'gaji_pokok' => $validated['gaji_pokok'],
        ]);

        return redirect()->back()->with('success', "Gaji pokok untuk {$user->name} berhasil diperbarui.");
    }

    /**
     * Halaman Slip Gaji Saya (Karyawan) - Blade View
     */
    public function myPayslips(Request $request): View
    {
        $payslips = Payslip::with('user')
            ->where('user_id', auth()->id())
            ->where('status', 'terbit')
            ->orderBy('periode', 'desc')
            ->get();

        return view('payroll.my-payslips', [
            'payslips' => $payslips,
        ]);
    }
}
