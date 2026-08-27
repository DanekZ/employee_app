<?php

use App\Models\Attendance;
use App\Models\User;

test('guests are redirected from approval absensi route', function () {
    $response = $this->get(route('approval.absensi'));

    $response->assertRedirect('/login');
});

test('karyawan role cannot access approval absensi route', function () {
    $karyawan = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($karyawan)->get(route('approval.absensi'));

    $response->assertStatus(403);
});

test('atasan role can view overall employee attendance', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create([
        'role' => 'karyawan',
        'atasan_id' => $atasan->id,
    ]);

    Attendance::create([
        'user_id' => $karyawan->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '08:00:00',
        'jam_keluar' => '17:00:00',
        'status' => 'hadir',
        'latitude' => '-6.200000',
        'longitude' => '106.816666',
    ]);

    $response = $this->actingAs($atasan)->get(route('approval.absensi'));

    $response->assertStatus(200);
    $response->assertSee($karyawan->name);
    $response->assertSee('hadir');
});

test('atasan can filter attendance by user_id', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawanA = User::factory()->create(['name' => 'Karyawan Alpha', 'role' => 'karyawan', 'atasan_id' => $atasan->id]);
    $karyawanB = User::factory()->create(['name' => 'Karyawan Beta', 'role' => 'karyawan', 'atasan_id' => $atasan->id]);

    Attendance::create([
        'user_id' => $karyawanA->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '08:00:00',
        'status' => 'hadir',
    ]);

    Attendance::create([
        'user_id' => $karyawanB->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '08:15:00',
        'status' => 'telat',
    ]);

    $response = $this->actingAs($atasan)->get(route('approval.absensi', ['user_id' => $karyawanA->id]));

    $response->assertStatus(200);
    $response->assertSee('Karyawan Alpha');
    // Memastikan data presensi yang tampil di tabel hanya milik Karyawan Alpha
    $response->assertSee('08:00:00');
    $response->assertDontSee('08:15:00');
});
