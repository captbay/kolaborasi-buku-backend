<?php

namespace Database\Factories;

use App\Models\buku_dijual;
use App\Models\penulis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\bukudijual_penulis_pivot>
 */
class bukudijual_penulis_pivotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'buku_dijual_id' => buku_dijual::all()->random()->id,
            'penulis_id' => penulis::all()->random()->id,
        ];
    }
}
