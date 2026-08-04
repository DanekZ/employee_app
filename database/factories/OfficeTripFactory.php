<?php

namespace Database\Factories;

use App\Models\OfficeTrip;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends Factory<OfficeTrip>
 */
class OfficeTripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tanggal' => fake()->date(),
            'tujuan_alamat' => fake()->address(),
            'jam_keluar' => '09:00',
            'jam_kembali' => '11:00',
            'alat_transportasi' => fake()->randomElement(['kendaraan_dinas', 'kendaraan_pribadi', 'transportasi_umum']),
            'alasan' => fake()->sentence(),
            'status' => 'pending'
        ];
    }
}
