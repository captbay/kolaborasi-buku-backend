<?php

namespace Database\Factories;

use App\Models\kategori;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\buku_dijual>
 */
class buku_dijualFactory extends Factory
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
            'harga' => $this->faker->numberBetween(10000, 100000),
            'tanggal_terbit' => $this->faker->date(),
            'cover_buku' => $this->faker->imageUrl(),
            'deskripsi' => $this->faker->paragraph(),
            'jumlah_halaman' => $this->faker->numberBetween(100, 500),
            'bahasa' => $this->faker->randomElement(['Indonesaia', 'Inggris', 'Spanyol']),
            'penerbit' => $this->faker->company(),
            'nama_file_buku' => $this->faker->word() . '.pdf',
            'file_buku' => $this->faker->imageUrl(),
            'active_flag' => $this->faker->numberBetween(0, 1),
        ];
    }
}
