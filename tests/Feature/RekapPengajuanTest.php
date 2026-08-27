<?php

use App\Models\LeaveRequest;
use App\Models\OfficeTrip;
use App\Models\OvertimeRequest;
use App\Models\User;

test('guests are redirected from approval rekap route', function () {
    $response = $this->get(route('approval.rekap'));

    $response->assertRedirect('/login');
});

test('karyawan role cannot access approval rekap route', function () {
    $karyawan = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($karyawan)->get(route('approval.rekap'));

    $response->assertStatus(403);
});

test('atasan and admin roles can view rekapitulasi pengajuan page', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $admin = User::factory()->create(['role' => 'admin']);
    $karyawan = User::factory()->create(['role' => 'karyawan', 'atasan_id' => $atasan->id]);

    LeaveRequest::create([
        'user_id' => $karyawan->id,
        'jenis' => 'tidak_masuk',
        'tujuan' => 'Keperluan Keluarga',
        'tanggal_mulai' => now()->toDateString(),
        'keterangan' => 'Ada acara keluarga',
        'status' => 'approved',
    ]);

    OvertimeRequest::create([
        'user_id' => $karyawan->id,
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '17:00:00',
        'jam_selesai' => '20:00:00',
        'lokasi_lembur' => 'Kantor Pusat',
        'alasan' => 'Project Deadline',
        'status' => 'pending',
    ]);

    OfficeTrip::create([
        'user_id' => $karyawan->id,
        'tanggal' => now()->toDateString(),
        'tujuan_alamat' => 'Klien Bandung',
        'jam_keluar' => '08:00:00',
        'jam_kembali' => '17:00:00',
        'alat_transportasi' => 'kendaraan_dinas',
        'alasan' => 'Meeting Klien',
        'status' => 'rejected',
    ]);

    // Atasan access test
    $responseAtasan = $this->actingAs($atasan)->get(route('approval.rekap'));
    $responseAtasan->assertStatus(200);
    $responseAtasan->assertSee('Rekapitulasi Pengajuan Karyawan');
    $responseAtasan->assertSee('Keperluan Keluarga');

    // Admin access test
    $responseAdmin = $this->actingAs($admin)->get(route('approval.rekap'));
    $responseAdmin->assertStatus(200);
    $responseAdmin->assertSee('Rekapitulasi Pengajuan Karyawan');
});

test('atasan and admin can filter rekap by status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $karyawan = User::factory()->create(['role' => 'karyawan']);

    LeaveRequest::create([
        'user_id' => $karyawan->id,
        'jenis' => 'tidak_masuk',
        'tujuan' => 'Rumah Sakit',
        'tanggal_mulai' => now()->toDateString(),
        'keterangan' => 'Demam tinggi',
        'status' => 'approved',
    ]);

    LeaveRequest::create([
        'user_id' => $karyawan->id,
        'jenis' => 'tidak_masuk',
        'tujuan' => 'Liburan',
        'tanggal_mulai' => now()->toDateString(),
        'keterangan' => 'Cuti tahunan',
        'status' => 'rejected',
    ]);

    $responseFilterApproved = $this->actingAs($admin)->get(route('approval.rekap', ['status' => 'approved']));
    $responseFilterApproved->assertStatus(200);
    $responseFilterApproved->assertSee('Rumah Sakit');
    $responseFilterApproved->assertDontSee('Liburan');
});
