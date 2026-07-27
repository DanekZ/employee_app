<?php

namespace Database\Factories;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
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
            'jenis' => 'tidak_masuk',
            'tujuan' => fake()->sentence(3),
            'tanggal_mulai' => fake()->date(),
            'keterangan' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
