<?php

namespace Database\Factories;

use Carbon\Carbon;
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
            'file' => '/cover_buku.png',
            'waktu_mulai' => Carbon::now(),
            'waktu_selesai' => Carbon::now()->addDays(30),
            'active_flag' => $this->faker->numberBetween(0, 1),
        ];
    }
}
