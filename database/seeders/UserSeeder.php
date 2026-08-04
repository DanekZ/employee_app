<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $atasan = User::create([
            'name' => 'Budi Atasan',
            'email' => 'atasan@sinarta.test',
            'password' => bcrypt('password'),
            'role' => 'atasan',
        ]);

        User::create([
            'name' => 'Zidane Karyawan',
            'email' => 'karyawan@sinarta.test',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'atasan_id' => $atasan->id,
        ]);
    }
}
