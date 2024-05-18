<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\konten_faq>
 */
class mouFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->word(),
            'kategori' => $this->faker->word(),
            'file_mou' => $this->faker->word(),
            'active_flag' => $this->faker->numberBetween(0, 1),
        ];
    }
}
