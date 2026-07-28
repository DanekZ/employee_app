<?php

namespace Database\Factories;

use App\Models\OvertimeRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OvertimeRequest>
 */
use App\Models\User;
class OvertimeRequestFactory extends Factory
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
            'jam_mulai' => '17:00',
            'jam_selesai' => '19:00',
            'lokasi_lembur' => fake()->randomElement(['Kantor Pusat', 'Kantor Cabang']),
            'alasan' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
