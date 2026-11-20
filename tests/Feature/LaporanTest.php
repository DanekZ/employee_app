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

test('admin role can access both laporan visualisasi and karyawan features', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $responseLaporan = $this->actingAs($admin)->get(route('approval.laporan'));
    $responseLaporan->assertStatus(200);
    $responseLaporan->assertSee('Laporan Visualisasi Presensi Karyawan');

    $responseAbsensi = $this->actingAs($admin)->get(route('absensi.index'));
    $responseAbsensi->assertStatus(200);
});

test('laporan includes attendance data and employee statistics for admin role users', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $adminUser = User::factory()->create([
        'name' => 'Admin Super',
        'role' => 'admin',
        'atasan_id' => $atasan->id,
    ]);

    Attendance::create([
        'user_id' => $adminUser->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '07:50:00',
        'jam_keluar' => '17:00:00',
        'status' => 'hadir',
    ]);

    $response = $this->actingAs($atasan)->get(route('approval.laporan'));

    $response->assertStatus(200);
    $response->assertSee('Admin Super');
    $response->assertSee('Sangat Disiplin');
});

test('laporan view renders complete attendance table with details and working duration', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create([
        'name' => 'Ahmad Fadli',
        'role' => 'karyawan',
        'atasan_id' => $atasan->id,
    ]);

    Attendance::create([
        'user_id' => $karyawan->id,
        'tanggal' => '2026-11-10',
        'jam_masuk' => '07:45:00',
        'jam_keluar' => '16:45:00',
        'status' => 'hadir',
        'latitude' => '-6.2088',
        'longitude' => '106.8456',
    ]);

    $response = $this->actingAs($atasan)->get(route('approval.laporan', [
        'tanggal_mulai' => '2026-11-01',
        'tanggal_selesai' => '2026-11-15',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Data Absensi Lengkap Karyawan');
    $response->assertSee('Ahmad Fadli');
    $response->assertSee('07:45:00');
    $response->assertSee('16:45:00');
    $response->assertSee('9 jam 0 menit');
    $response->assertSee('Tepat Waktu');
    $response->assertSee('Cetak Laporan / PDF');
});

test('laporan table displays late minutes when employee arrives late', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create([
        'name' => 'Dewi Lestari',
        'role' => 'karyawan',
        'atasan_id' => $atasan->id,
    ]);

    Attendance::create([
        'user_id' => $karyawan->id,
        'tanggal' => '2026-11-12',
        'jam_masuk' => '08:25:00',
        'jam_keluar' => '17:00:00',
        'status' => 'telat',
    ]);

    $response = $this->actingAs($atasan)->get(route('approval.laporan', [
        'tanggal_mulai' => '2026-11-01',
        'tanggal_selesai' => '2026-11-15',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Dewi Lestari');
    $response->assertSee('08:25:00');
    $response->assertSee('Terlambat 25 menit');
});

test('laporan table filters by date range correctly', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create([
        'name' => 'Rian Hidayat',
        'role' => 'karyawan',
        'atasan_id' => $atasan->id,
    ]);

    // Record inside range
    Attendance::create([
        'user_id' => $karyawan->id,
        'tanggal' => '2026-11-05',
        'jam_masuk' => '07:50:00',
        'jam_keluar' => '17:00:00',
        'status' => 'hadir',
    ]);

    // Record outside range
    Attendance::create([
        'user_id' => $karyawan->id,
        'tanggal' => '2026-10-25',
        'jam_masuk' => '07:55:00',
        'jam_keluar' => '17:00:00',
        'status' => 'hadir',
    ]);

    $response = $this->actingAs($atasan)->get(route('approval.laporan', [
        'tanggal_mulai' => '2026-11-01',
        'tanggal_selesai' => '2026-11-10',
    ]));

    $response->assertStatus(200);
    $response->assertSee('05 Nov 2026');
    $response->assertDontSee('25 Okt 2026');
});

test('approval absensi supports filtering by date range and has cetak pdf button', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create([
        'name' => 'Fajar Pratama',
        'role' => 'karyawan',
        'atasan_id' => $atasan->id,
    ]);

    Attendance::create([
        'user_id' => $karyawan->id,
        'tanggal' => '2026-11-03',
        'jam_masuk' => '07:58:00',
        'jam_keluar' => '17:00:00',
        'status' => 'hadir',
    ]);

    Attendance::create([
        'user_id' => $karyawan->id,
        'tanggal' => '2026-10-15',
        'jam_masuk' => '08:00:00',
        'jam_keluar' => '17:00:00',
        'status' => 'hadir',
    ]);

    $response = $this->actingAs($atasan)->get(route('approval.absensi', [
        'tanggal_mulai' => '2026-11-01',
        'tanggal_selesai' => '2026-11-10',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Fajar Pratama');
    $response->assertSee('07:58:00');
    $response->assertDontSee('15 Oktober 2026');
    $response->assertSee('Cetak Laporan / PDF');
});
