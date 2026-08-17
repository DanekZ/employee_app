<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Atasan: Novi
        $novi = User::updateOrCreate(
            ['email' => 'novi@sinarta.test'],
            [
                'name' => 'Novi',
                'password' => Hash::make('password'),
                'role' => 'atasan',
            ]
        );

        // 2. Karyawan biasa: Sela Saputri
        User::updateOrCreate(
            ['email' => 'sela@sinarta.test'],
            [
                'name' => 'Sela Saputri',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'atasan_id' => $novi->id,
            ]
        );

        // 3. Karyawan biasa: Denissa Putri
        User::updateOrCreate(
            ['email' => 'denissa@sinarta.test'],
            [
                'name' => 'Denissa Putri',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'atasan_id' => $novi->id,
            ]
        );
    }
}
