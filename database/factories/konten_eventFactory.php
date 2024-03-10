<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\konten_event>
 */
class konten_eventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipe' => 'IMAGE',
            'file' => '/cover_buku.jpg',
            'waktu_mulai' => $this->faker->dateTime(),
            'waktu_selesai' => $this->faker->dateTime(),
            'active_flag' => $this->faker->numberBetween(0, 1),
        ];
    }
}
