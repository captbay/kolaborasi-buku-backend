<?php

namespace Database\Factories;

use App\Models\buku_dijual;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\storage_buku_dijual>
 */
class storage_buku_dijualFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'buku_dijual_id' => buku_dijual::all()->random()->id,
            'tipe' => "IMAGE",
            'nama_file' => $this->faker->word() . '.jpg',
            'nama_generate' => $this->faker->imageUrl(),
        ];
    }
}
