<?php

namespace Database\Factories;

use App\Models\buku_kolaborasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\bab_buku_kolaborasi>
 */
class bab_buku_kolaborasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'buku_kolaborasi_id' => buku_kolaborasi::all()->random()->id,
            'no_bab' => $this->faker->numberBetween(1, 10),
            'judul' => $this->faker->word(),
            'harga' => $this->faker->numberBetween(10000, 100000),
            'durasi_pembuatan' => $this->faker->numberBetween(30, 40), // dalam hari
            'deskripsi'    => $this->faker->paragraph(),
            'active_flag' => $this->faker->boolean(),
        ];
    }
}
