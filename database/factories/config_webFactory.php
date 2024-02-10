<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\config_web>
 */
class config_webFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->word(),
            'tipe' => $this->faker->word(),
            'value' => $this->faker->url(),
            'active_flag' => $this->faker->numberBetween(0, 1),
        ];
    }
}
