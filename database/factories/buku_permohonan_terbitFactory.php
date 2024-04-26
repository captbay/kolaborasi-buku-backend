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
        return [
            'user_id' => User::where('role', '!=', 'ADMIN')->inRandomOrder()->first()->id,
            'judul' => $this->faker->word(),
            'deskripsi' => $this->faker->paragraph(5),
            'cover_buku' => '/cover_buku_' . rand(1, 3) . '.jpg',
            'file_buku' => '/buku_' . rand(1, 10) . '.pdf',
            'file_mou' => '/buku_' . rand(1, 10) . '.pdf',
            'dijual' => 0,
            'isbn' => $this->faker->isbn13(),
        ];
    }
}
