<?php

namespace Database\Factories;

use App\Models\transaksi_kolaborasi_buku;
use App\Models\transaksi_paket_penerbitan;
use App\Models\transaksi_penjualan_buku;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\notifikasi>
 */
class NotifikasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipes = $this->faker->randomElement(['PEMBELIAN', 'KOLABORASI', 'PENERBITAN']);

        if ($tipes == 'PEMBELIAN') {
            $tabelId = transaksi_penjualan_buku::all()->random()->id;
        } else if ($tipes == 'KOLABORASI') {
            $tabelId = transaksi_kolaborasi_buku::all()->random()->id;
        } else {
            $tabelId = transaksi_paket_penerbitan::all()->random()->id;
        }

        return [
            'user_id' => User::all()->random()->id,
            'tipe' => $tipes,
            'tabel_id' => $tabelId,
            'judul' => $this->faker->word(),
            'pesan' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['NEW', 'OPENED']),
        ];
    }
}
