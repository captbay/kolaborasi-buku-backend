<?php

namespace Database\Factories;

use App\Models\buku_dijual;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\testimoni_pembeli>
 */
class testimoni_pembeliFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'buku_dijual_id' => buku_dijual::all()->random()->id,
            'ulasan' => $this->faker->randomElement([$this->faker->text(), null]),
            'rating' => $this->faker->numberBetween(1, 5),
            'active_flag' => $this->faker->numberBetween(0, 1),
        ];
    }
}
