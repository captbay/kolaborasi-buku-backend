<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\transaksi_penjualan_buku>
 */
class transaksi_penjualan_bukuFactory extends Factory
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
            $date_time_lunas = Carbon::now();
        } else {
            $date_time_lunas = null;
        }

        return [
            'user_id' => User::where('role', '!=', 'ADMIN')->inRandomOrder()->first()->id,
            'no_transaksi' => "P" . $this->faker->randomNumber(1, 1000),
            'total_harga' => $this->faker->numberBetween(10000, 1000000),
            'status' => $status,
            'foto_bukti_bayar' => "/cover_buku.jpg",
            'date_time_lunas' => $date_time_lunas,
        ];
    }
}
