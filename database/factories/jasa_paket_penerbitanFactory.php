<?php

namespace Database\Factories;

use App\Models\jasa_tambahan;
use App\Models\paket_penerbitan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\jasa_paket_penerbitan>
 */
class jasa_paket_penerbitanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'paket_penerbitan_id' => paket_penerbitan::all()->random()->id,
            'jasa_tambahan_id' => jasa_tambahan::all()->random()->id
        ];
    }
}
