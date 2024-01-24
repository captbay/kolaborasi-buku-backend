<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\buku_permohonan_terbit>
 */
class buku_permohonan_terbitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['ACCEPTED', 'REVIEW', 'REVISI', 'REJECTED']);

        return [
            'user_id' => User::all()->random()->id,
            'judul' => $this->faker->word(),
            'deskripsi' => $this->faker->paragraph(),
            'persen_bagi_hasil' => $this->faker->numberBetween(1, 50),
            'status' => $status,
            'file_buku' => $this->faker->imageUrl(),
        ];
    }
}
