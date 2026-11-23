<?php

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

test('employee can start break after check in', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '07:55:00',
        'status' => 'hadir',
    ]);

    $response = $this->actingAs($user)->post(route('absensi.break.start'));

    $response->assertSessionHas('success');
    $attendance->refresh();
    expect($attendance->jam_istirahat_mulai)->not->toBeNull();
});

test('employee cannot start break before check in', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('absensi.break.start'));

    $response->assertSessionHas('error');
});

test('employee cannot start break twice in the same day', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    Attendance::create([
        'user_id' => $user->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '07:55:00',
        'jam_istirahat_mulai' => '12:05:00',
        'status' => 'hadir',
    ]);

    $response = $this->actingAs($user)->post(route('absensi.break.start'));

    $response->assertSessionHas('error', 'Anda sudah mencatat mulai istirahat hari ini.');
});

test('employee returning from break on or before 13:00 is marked tepat_waktu', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '07:55:00',
        'jam_istirahat_mulai' => '12:00:00',
        'status' => 'hadir',
    ]);

    // Freeze time to 12:55:00
    Carbon::setTestNow(now()->startOfDay()->addHours(12)->addMinutes(55));

    $response = $this->actingAs($user)->post(route('absensi.break.end'));

    $response->assertSessionHas('success');
    $attendance->refresh();
    expect($attendance->jam_istirahat_selesai)->toBe('12:55:00');
    expect($attendance->status_istirahat)->toBe('tepat_waktu');
    expect($attendance->status)->toBe('hadir');

    Carbon::setTestNow();
});

test('employee returning from break after 13:00 is marked as telat', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '07:55:00',
        'jam_istirahat_mulai' => '12:00:00',
        'status' => 'hadir',
    ]);

    // Freeze time to 13:15:00
    Carbon::setTestNow(now()->startOfDay()->addHours(13)->addMinutes(15));

    $response = $this->actingAs($user)->post(route('absensi.break.end'));

    $response->assertSessionHas('error');
    $attendance->refresh();
    expect($attendance->jam_istirahat_selesai)->toBe('13:15:00');
    expect($attendance->status_istirahat)->toBe('telat');
    // Status kehadiran utama juga berubah menjadi telat
    expect($attendance->status)->toBe('telat');
    expect($attendance->keterangan_ketepatan)->toBe('Terlambat Istirahat');

    Carbon::setTestNow();
});

test('employee cannot checkout while still on break', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    Attendance::create([
        'user_id' => $user->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '07:55:00',
        'jam_istirahat_mulai' => '12:00:00',
        'status' => 'hadir',
    ]);

    $response = $this->actingAs($user)->post(route('absensi.checkout'), [
        'latitude' => '-6.2088',
        'longitude' => '106.8456',
    ]);

    $response->assertSessionHas('error', 'Anda masih dalam status istirahat. Silakan absen selesai istirahat terlebih dahulu.');
});

test('employee can checkout after break is finished', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '08:00:00',
        'jam_istirahat_mulai' => '12:00:00',
        'jam_istirahat_selesai' => '13:00:00',
        'status' => 'hadir',
    ]);

    Carbon::setTestNow(now()->startOfDay()->addHours(17));

    $response = $this->actingAs($user)->post(route('absensi.checkout'), [
        'latitude' => '-6.2088',
        'longitude' => '106.8456',
    ]);

    $response->assertSessionHas('success');
    $attendance->refresh();
    expect($attendance->jam_keluar)->toBe('17:00:00');
    // Total durasi: 9 jam - 1 jam istirahat = 8 jam 0 menit
    expect($attendance->durasi_kerja)->toBe('8 jam 0 menit');

    Carbon::setTestNow();
});

test('keterangan ketepatan displays clean text without late minutes', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $attendanceLateMasuk = Attendance::create([
        'user_id' => $user->id,
        'tanggal' => '2026-11-20',
        'jam_masuk' => '08:05:30',
        'status' => 'telat',
    ]);

    expect($attendanceLateMasuk->keterangan_ketepatan)->toBe('Terlambat Masuk');

    $attendanceLateIstirahat = Attendance::create([
        'user_id' => $user->id,
        'tanggal' => '2026-11-21',
        'jam_masuk' => '08:00:00',
        'jam_istirahat_mulai' => '12:00:00',
        'jam_istirahat_selesai' => '13:07:45',
        'status' => 'telat',
        'status_istirahat' => 'telat',
    ]);

    expect($attendanceLateIstirahat->keterangan_ketepatan)->toBe('Terlambat Istirahat');

    $attendanceLateBoth = Attendance::create([
        'user_id' => $user->id,
        'tanggal' => '2026-11-22',
        'jam_masuk' => '08:10:00',
        'jam_istirahat_mulai' => '12:00:00',
        'jam_istirahat_selesai' => '13:10:00',
        'status' => 'telat',
        'status_istirahat' => 'telat',
    ]);

    expect($attendanceLateBoth->keterangan_ketepatan)->toBe('Terlambat Masuk, Terlambat Istirahat');
});
