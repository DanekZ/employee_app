<?php

use App\Models\User;
use App\Models\OfficeTrip;

test('karyawan bisa melihat halaman riwayat dinas', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->get(route('dinas.index'));

    $response->assertOk();
});

test('karyawan bisa mengajukan dinas', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('dinas.store'), [
        'tanggal' => '2026-08-01',
        'tujuan_alamat' => 'Kantor Dinas Kependudukan',
        'jam_keluar' => '09:00',
        'jam_kembali' => '11:00',
        'alat_transportasi' => 'kendaraan_dinas',
        'alasan' => 'Mengurus dokumen perusahaan',
    ]);

    $response->assertRedirect(route('dinas.index'));

    $this->assertDatabaseHas('office_trips', [
        'user_id' => $user->id,
        'tujuan_alamat' => 'Kantor Dinas Kependudukan',
        'status' => 'pending',
    ]);
});

test('pengajuan dinas gagal kalau jam kembali sebelum jam keluar', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('dinas.store'), [
        'tanggal' => '2026-08-01',
        'tujuan_alamat' => 'Kantor Dinas',
        'jam_keluar' => '11:00',
        'jam_kembali' => '09:00',
        'alat_transportasi' => 'kendaraan_pribadi',
        'alasan' => 'Test',
    ]);

    $response->assertSessionHasErrors(['jam_kembali']);
});

test('pengajuan dinas gagal kalau alat transportasi tidak valid', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('dinas.store'), [
        'tanggal' => '2026-08-01',
        'tujuan_alamat' => 'Kantor Dinas',
        'jam_keluar' => '09:00',
        'jam_kembali' => '11:00',
        'alat_transportasi' => 'ojek_online',
        'alasan' => 'Test',
    ]);

    $response->assertSessionHasErrors(['alat_transportasi']);
});

test('pengajuan dinas gagal kalau field wajib kosong', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('dinas.store'), [
        'tanggal' => '',
        'tujuan_alamat' => '',
        'jam_keluar' => '',
        'jam_kembali' => '',
        'alat_transportasi' => '',
        'alasan' => '',
    ]);

    $response->assertSessionHasErrors(['tanggal', 'tujuan_alamat', 'jam_keluar', 'jam_kembali', 'alat_transportasi', 'alasan']);
});

test('dinas hanya menampilkan data milik user yang login', function () {
    $userA = User::factory()->create(['role' => 'karyawan']);
    $userB = User::factory()->create(['role' => 'karyawan']);

    OfficeTrip::factory()->create(['user_id' => $userA->id]);
    OfficeTrip::factory()->create(['user_id' => $userB->id]);

    $response = $this->actingAs($userA)->get(route('dinas.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('officeTrip/index')
        ->has('officeTrips', 1)
    );
});