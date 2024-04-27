<?php

namespace Database\Factories;

use App\Models\kategori;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\buku_kolaborasi>
 */
class buku_kolaborasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $juduls = $this->faker->sentence();

        // make slug from juduls
        $slugs = Str::slug($juduls);

        return [
            'kategori_id' => kategori::all()->random()->id,
            'slug' => $slugs,
            'judul' => $juduls,
            'cover_buku' => '/cover_buku_' . rand(1, 3) . '.jpg',
            'deskripsi' => $this->faker->paragraph(10),
            'jumlah_bab' => $this->faker->numberBetween(1, 10),
            'bahasa' => $this->faker->randomElement(['Indonesaia', 'Inggris']),
            'active_flag' => $this->faker->boolean(),
            'dijual' => 0,
        ];
    }
}
