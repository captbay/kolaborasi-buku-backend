<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\paket_penerbitan>
 */
class paket_penerbitanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->word(),
            'harga' => $this->faker->numberBetween(10000, 1000000),
            'deskripsi' => $this->faker->paragraph(),
            'waktu_mulai' => $this->faker->dateTime(),
            'waktu_selesai' => $this->faker->dateTime(),
        ];
    }
}
