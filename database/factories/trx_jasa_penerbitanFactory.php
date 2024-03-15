<?php

namespace Database\Factories;

use App\Models\jasa_tambahan;
use App\Models\transaksi_paket_penerbitan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\jasa_transaksi_paket_penerbitan>
 */
class trx_jasa_penerbitanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jasa_tambahan_id' => jasa_tambahan::all()->random()->id,
            'transaksi_paket_penerbitan_id' => transaksi_paket_penerbitan::all()->random()->id,
        ];
    }
}
