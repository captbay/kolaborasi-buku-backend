<?php

namespace Database\Factories;

use App\Models\buku_dijual;
use App\Models\transaksi_penjualan_buku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\list_transaksi_buku>
 */
class list_transaksi_bukuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaksi_penjualan_buku_id' => transaksi_penjualan_buku::all()->random()->id,
            'buku_dijual_id' => buku_dijual::all()->random()->id,
        ];
    }
}
