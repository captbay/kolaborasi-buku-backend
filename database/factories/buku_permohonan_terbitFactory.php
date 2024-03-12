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
            'user_id' => User::where('role', '!=', 'ADMIN')->inRandomOrder()->first()->id,
            'judul' => $this->faker->word(),
            'deskripsi' => $this->faker->paragraph(),
            'persen_bagi_hasil' => $this->faker->numberBetween(1, 50),
            'status' => $status,
            'cover_buku' => '/cover_buku.jpg',
            'file_buku' => '/buku_final_temp.pdf',
            'file_mou' => '/buku_final_temp.pdf',
            'dijual' => 0,
        ];
    }
}
