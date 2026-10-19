<?php

use App\Models\OfficeTrip;
use App\Models\OvertimeRequest;
use App\Models\Payslip;
use App\Models\SalarySetting;
use App\Models\User;
use App\Services\PayslipService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('atasan can view payroll page', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);

    $response = $this->actingAs($atasan)->get(route('penggajian.index'));

    $response->assertStatus(200);
});

test('karyawan cannot access atasan payroll management page', function () {
    $karyawan = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($karyawan)->get(route('penggajian.index'));

    $response->assertStatus(403);
});

test('atasan can update global salary settings and employee base salary', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create(['role' => 'karyawan', 'gaji_pokok' => 3000000]);

    // Update settings
    $responseSettings = $this->actingAs($atasan)->post(route('penggajian.update-settings'), [
        'rate_lembur_per_jam' => 25000,
        'rate_dinas_per_jam' => 30000,
        'potongan_per_hari_alpha' => 100000,
        'potongan_per_hari_izin' => 50000,
        'uang_saku_dinas_per_hari' => 0,
    ]);

    $responseSettings->assertRedirect();
    $this->assertDatabaseHas('salary_settings', [
        'rate_lembur_per_jam' => 25000,
        'rate_dinas_per_jam' => 30000,
    ]);

    // Update employee salary
    $responseSalary = $this->actingAs($atasan)->patch(route('penggajian.update-employee-salary', $karyawan->id), [
        'gaji_pokok' => 4500000,
    ]);

    $responseSalary->assertRedirect();
    $this->assertDatabaseHas('users', [
        'id' => $karyawan->id,
        'gaji_pokok' => 4500000,
    ]);
});

test('payslip calculation calculates base salary overtime and dinas correctly', function () {
    SalarySetting::create([
        'rate_lembur_per_jam' => 20000,
        'rate_dinas_per_jam' => 25000,
        'potongan_per_hari_alpha' => 100000,
        'potongan_per_hari_izin' => 50000,
    ]);

    $employee = User::factory()->create([
        'role' => 'karyawan',
        'gaji_pokok' => 5000000,
    ]);

    // Create approved overtime (3 hours)
    OvertimeRequest::create([
        'user_id' => $employee->id,
        'tanggal' => '2026-09-10',
        'jam_mulai' => '17:00:00',
        'jam_selesai' => '20:00:00',
        'lokasi_lembur' => 'Kantor',
        'alasan' => 'Project launch',
        'status' => 'approved',
    ]);

    // Create approved office trip (4 hours)
    OfficeTrip::create([
        'user_id' => $employee->id,
        'tanggal' => '2026-09-15',
        'jam_keluar' => '08:00:00',
        'jam_kembali' => '12:00:00',
        'tujuan_alamat' => 'Client Site',
        'alasan' => 'Meeting',
        'alat_transportasi' => 'kendaraan_dinas',
        'status' => 'approved',
    ]);

    $service = new PayslipService;
    $payslip = $service->generate($employee, '2026-09');

    expect($payslip->gaji_pokok)->toEqual('5000000.00');
    expect($payslip->total_jam_lembur)->toEqual(3);
    expect($payslip->nominal_lembur)->toEqual('60000.00'); // 3 * 20000
    expect($payslip->nominal_dinas)->toEqual('100000.00'); // 4 * 25000
    expect($payslip->total_gaji_bersih)->toEqual('5160000.00'); // 5000000 + 60000 + 100000
});

test('employee can view published payslips', function () {
    $employee = User::factory()->create(['role' => 'karyawan']);

    // Draft payslip (should not show to employee)
    Payslip::create([
        'user_id' => $employee->id,
        'periode' => '2026-08',
        'gaji_pokok' => 4000000,
        'total_gaji_bersih' => 4000000,
        'status' => 'draft',
    ]);

    // Published payslip (should show)
    Payslip::create([
        'user_id' => $employee->id,
        'periode' => '2026-09',
        'gaji_pokok' => 4000000,
        'total_gaji_bersih' => 4000000,
        'status' => 'terbit',
        'diterbitkan_at' => now(),
    ]);

    $response = $this->actingAs($employee)->get(route('penggajian.my-payslips'));

    $response->assertStatus(200);
});
