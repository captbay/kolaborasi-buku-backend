<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\kategori>
 */
class kategoriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $namas = $this->faker->unique()->word();

        // make slug from juduls
        $slugs = Str::slug($namas);

        return [
            'nama' => $namas,
            'slug' => $slugs,
            'deskripsi' => $this->faker->text(),
        ];
    }
}
