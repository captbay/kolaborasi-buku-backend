<?php

namespace Database\Factories;

use App\Models\buku_permohonan_terbit;
use App\Models\paket_penerbitan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\transaksi_paket_penerbitan>
 */
class transaksi_paket_penerbitanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['DONE', 'PROGRESS', 'FAILED', 'UPLOADED']);

        if ($status == 'DONE') {
            $date_time_lunas = $this->faker->dateTime();
        } else {
            $date_time_lunas = null;
        }

        return [
            'user_id' => User::where('role', '!=', 'ADMIN')->inRandomOrder()->first()->id,
            'paket_penerbitan_id' => paket_penerbitan::all()->random()->id,
            'buku_permohonan_terbit_id' => buku_permohonan_terbit::all()->random()->id,
            'no_transaksi' => "T" . $this->faker->randomNumber(1, 1000),
            'total_harga' => $this->faker->numberBetween(10000, 1000000),
            'status' => $status,
            'foto_bukti_bayar' => $this->faker->imageUrl(),
            'date_time_lunas' => $date_time_lunas,
        ];
    }
}
