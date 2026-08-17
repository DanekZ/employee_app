<?php

use App\Models\OvertimeRequest;
use App\Models\User;

test('karyawan bisa melihat halaman riwayat lembur', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->get(route('lembur.index'));

    $response->assertOk();
});

test('karyawan bisa mengajukan lembur', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('lembur.store'), [
        'tanggal' => '2026-08-01',
        'jam_mulai' => '17:00',
        'jam_selesai' => '19:00',
        'lokasi_lembur' => 'Kantor Pusat',
        'alasan' => 'Menyelesaikan laporan bulanan',
    ]);

    $response->assertRedirect(route('lembur.index'));

    $this->assertDatabaseHas('overtime_requests', [
        'user_id' => $user->id,
        'lokasi_lembur' => 'Kantor Pusat',
        'status' => 'pending',
    ]);
});

test('pengajuan lembur gagal kalau jam selesai sebelum jam mulai', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('lembur.store'), [
        'tanggal' => '2026-08-01',
        'jam_mulai' => '19:00',
        'jam_selesai' => '17:00',
        'lokasi_lembur' => 'Kantor Pusat',
        'alasan' => 'Test',
    ]);

    $response->assertSessionHasErrors(['jam_selesai']);
});

test('pengajuan lembur gagal kalau field wajib kosong', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('lembur.store'), [
        'tanggal' => '',
        'jam_mulai' => '',
        'jam_selesai' => '',
        'lokasi_lembur' => '',
        'alasan' => '',
    ]);

    $response->assertSessionHasErrors(['tanggal', 'jam_mulai', 'jam_selesai', 'lokasi_lembur', 'alasan']);
});

test('lembur hanya menampilkan data milik user yang login', function () {
    $userA = User::factory()->create(['role' => 'karyawan']);
    $userB = User::factory()->create(['role' => 'karyawan']);

    OvertimeRequest::factory()->create(['user_id' => $userA->id]);
    OvertimeRequest::factory()->create(['user_id' => $userB->id]);

    $response = $this->actingAs($userA)->get(route('lembur.index'));

    $response->assertViewIs('lembur.index')
        ->assertViewHas('overtimeRequests', fn ($requests) => count($requests) === 1);
});
