<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\hubungi_kami>
 */
class hubungi_kamiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'subjek' => $this->faker->sentence(),
            'pesan' => $this->faker->sentence(),
        ];
    }
}
