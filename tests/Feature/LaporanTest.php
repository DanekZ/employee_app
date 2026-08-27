<?php

use App\Models\Attendance;
use App\Models\User;

test('guests are redirected from approval laporan route', function () {
    $response = $this->get(route('approval.laporan'));

    $response->assertRedirect('/login');
});

test('karyawan role cannot access approval laporan route', function () {
    $karyawan = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($karyawan)->get(route('approval.laporan'));

    $response->assertStatus(403);
});

test('atasan role can view laporan visualisasi page and statistics', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'karyawan',
        'atasan_id' => $atasan->id,
    ]);

    Attendance::create([
        'user_id' => $karyawan->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '07:55:00',
        'jam_keluar' => '17:00:00',
        'status' => 'hadir',
    ]);

    $response = $this->actingAs($atasan)->get(route('approval.laporan'));

    $response->assertStatus(200);
    $response->assertSee('Laporan Visualisasi Presensi Karyawan');
    $response->assertSee('Budi Santoso');
    $response->assertSee('Sangat Disiplin');
});

test('atasan can filter laporan by custom date range', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create([
        'name' => 'Siti Rahma',
        'role' => 'karyawan',
        'atasan_id' => $atasan->id,
    ]);

    Attendance::create([
        'user_id' => $karyawan->id,
        'tanggal' => '2026-08-10',
        'jam_masuk' => '08:15:00',
        'status' => 'telat',
    ]);

    $response = $this->actingAs($atasan)->get(route('approval.laporan', [
        'tanggal_mulai' => '2026-08-01',
        'tanggal_selesai' => '2026-08-15',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Siti Rahma');
    $response->assertSee('Perlu Evaluasi');
});
