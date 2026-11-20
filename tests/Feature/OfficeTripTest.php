<?php

use App\Models\OfficeTrip;
use App\Models\User;

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

    $response->assertViewIs('dinas.index')
        ->assertViewHas('officeTrips', fn ($trips) => count($trips) === 1);
});

test('atasan can approve office trip request', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create(['role' => 'karyawan', 'atasan_id' => $atasan->id]);
    $officeTrip = OfficeTrip::factory()->create(['user_id' => $karyawan->id, 'status' => 'pending']);

    $response = $this->actingAs($atasan)->patch(route('dinas.approve', $officeTrip));

    $response->assertRedirect();
    $this->assertDatabaseHas('office_trips', [
        'id' => $officeTrip->id,
        'status' => 'approved',
        'approved_by' => $atasan->id,
    ]);
});

test('atasan can reject office trip request', function () {
    $atasan = User::factory()->create(['role' => 'atasan']);
    $karyawan = User::factory()->create(['role' => 'karyawan', 'atasan_id' => $atasan->id]);
    $officeTrip = OfficeTrip::factory()->create(['user_id' => $karyawan->id, 'status' => 'pending']);

    $response = $this->actingAs($atasan)->patch(route('dinas.reject', $officeTrip));

    $response->assertRedirect();
    $this->assertDatabaseHas('office_trips', [
        'id' => $officeTrip->id,
        'status' => 'rejected',
        'approved_by' => $atasan->id,
    ]);
});

test('karyawan can submit office trip with kendaraan pribadi bawa sendiri', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('dinas.store'), [
        'tanggal' => '2026-08-01',
        'tujuan_alamat' => 'Kantor Cabang',
        'jam_keluar' => '09:00',
        'jam_kembali' => '12:00',
        'alat_transportasi' => 'kendaraan_pribadi',
        'peran_kendaraan' => 'bawa_sendiri',
        'alasan' => 'Dinas membawa motor sendiri',
    ]);

    $response->assertRedirect(route('dinas.index'));

    $this->assertDatabaseHas('office_trips', [
        'user_id' => $user->id,
        'alat_transportasi' => 'kendaraan_pribadi',
        'peran_kendaraan' => 'bawa_sendiri',
        'menumpang_user_id' => null,
    ]);
});

test('karyawan can submit office trip with kendaraan pribadi menumpang rekan kerja', function () {
    $driver = User::factory()->create(['role' => 'karyawan']);
    $passenger = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($passenger)->post(route('dinas.store'), [
        'tanggal' => '2026-08-01',
        'tujuan_alamat' => 'Kantor Cabang',
        'jam_keluar' => '09:00',
        'jam_kembali' => '12:00',
        'alat_transportasi' => 'kendaraan_pribadi',
        'peran_kendaraan' => 'menumpang',
        'menumpang_user_id' => $driver->id,
        'alasan' => 'Dinas menumpang motor rekan',
    ]);

    $response->assertRedirect(route('dinas.index'));

    $this->assertDatabaseHas('office_trips', [
        'user_id' => $passenger->id,
        'alat_transportasi' => 'kendaraan_pribadi',
        'peran_kendaraan' => 'menumpang',
        'menumpang_user_id' => $driver->id,
    ]);
});

test('pengajuan dinas gagal jika menumpang tetapi tidak memilih rekan kerja', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $response = $this->actingAs($user)->post(route('dinas.store'), [
        'tanggal' => '2026-08-01',
        'tujuan_alamat' => 'Kantor Cabang',
        'jam_keluar' => '09:00',
        'jam_kembali' => '12:00',
        'alat_transportasi' => 'kendaraan_pribadi',
        'peran_kendaraan' => 'menumpang',
        'menumpang_user_id' => '',
        'alasan' => 'Dinas test',
    ]);

    $response->assertSessionHasErrors(['menumpang_user_id']);
});
