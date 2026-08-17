<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});

test('authenticated users are redirected from home route', function () {
    $user = User::factory()->create(['role' => 'karyawan']);
    $this->actingAs($user);

    $response = $this->get('/');
    $response->assertRedirect(route('absensi.index'));
});
