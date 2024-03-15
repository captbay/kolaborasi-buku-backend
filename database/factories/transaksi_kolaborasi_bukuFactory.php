<?php

namespace Database\Factories;

use App\Models\bab_buku_kolaborasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\transaksi_kolaborasi_buku>
 */
class transaksi_kolaborasi_bukuFactory extends Factory
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
            'bab_buku_kolaborasi_id' => bab_buku_kolaborasi::all()->random()->id,
            'no_transaksi' => "K" . $this->faker->randomNumber(1, 1000),
            'total_harga' => $this->faker->numberBetween(10000, 1000000),
            'status' => $status,
            'foto_bukti_bayar' => "/cover_buku.jpg",
            'date_time_lunas' => $date_time_lunas,
        ];
    }
}
