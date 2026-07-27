<?php

use App\Models\User;
use App\Models\LeaveRequest;

test('karyawan bisa melihat halaman riwayat izin', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->get(route('izin.index'));

    $response->assertOk();
});

test('karyawan bisa mengajukan izin tidak masuk kerja', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('izin.store'), [
        'jenis' => 'tidak_masuk',
        'tujuan' => 'Sakit',
        'tanggal_mulai' => '2026-08-01',
        'tanggal_selesai' => '2026-08-02',
        'keterangan' => 'Demam tinggi',
    ]);

    $response->assertRedirect(route('izin.index'));

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $user->id,
        'jenis' => 'tidak_masuk',
        'tujuan' => 'Sakit',
        'status' => 'pending',
    ]);
});

test('karyawan bisa mengajukan izin datang terlambat', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('izin.store'), [
        'jenis' => 'terlambat',
        'tujuan' => 'Macet',
        'tanggal_mulai' => '2026-08-01',
        'durasi_menit' => 30,
        'keterangan' => 'Ada kecelakaan di jalan',
    ]);

    $response->assertRedirect(route('izin.index'));

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $user->id,
        'jenis' => 'terlambat',
        'durasi_menit' => 30,
    ]);
});

test('karyawan bisa mengajukan izin keluar kantor sementara', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('izin.store'), [
        'jenis' => 'keluar_kantor',
        'tujuan' => 'Urus dokumen',
        'tanggal_mulai' => '2026-08-01',
        'jam_mulai' => '10:00',
        'jam_selesai' => '11:30',
        'keterangan' => 'Ke kantor dinas kependudukan',
    ]);

    $response->assertRedirect(route('izin.index'));

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $user->id,
        'jenis' => 'keluar_kantor',
    ]);
});

test('pengajuan izin gagal kalau field wajib kosong', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('izin.store'), [
        'jenis' => '',
        'tujuan' => '',
        'tanggal_mulai' => '',
        'keterangan' => '',
    ]);

    $response->assertSessionHasErrors(['jenis', 'tujuan', 'tanggal_mulai', 'keterangan']);
});

test('izin hanya menampilkan data milik user yang login', function () {
    $userA = User::factory()->create(['role' => 'karyawan']);
    $userB = User::factory()->create(['role' => 'karyawan']);

    LeaveRequest::factory()->create(['user_id' => $userA->id]);
    LeaveRequest::factory()->create(['user_id' => $userB->id]);

    $response = $this->actingAs($userA)->get(route('izin.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('leave/index')
        ->has('leaveRequests', 1)
    );
});