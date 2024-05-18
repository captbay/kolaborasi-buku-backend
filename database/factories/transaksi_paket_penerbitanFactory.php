<?php

namespace Database\Factories;

use App\Models\buku_permohonan_terbit;
use App\Models\paket_penerbitan;
use App\Models\User;
use Carbon\Carbon;
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
        $status = $this->faker->randomElement(['REVIEW', 'TERIMA DRAFT', 'DP UPLOADED', 'DP TIDAK SAH', 'INPUT ISBN', 'DRAFT SELESAI', 'PELUNASAN UPLOADED', 'PELUNASAN TIDAK SAH', 'SIAP TERBIT']);

        if ($status == 'SIAP TERBIT') {
            $date_time_dp_lunas = Carbon::now();
            $date_time_lunas = Carbon::now();
        } else if ($status == 'INPUT ISBN') {
            $date_time_dp_lunas = Carbon::now();
            $date_time_lunas = null;
        } else {
            $date_time_dp_lunas = null;
            $date_time_lunas = null;
        }

        return [
            'user_id' => User::where('role', '!=', 'ADMIN')->inRandomOrder()->first()->id,
            'paket_penerbitan_id' => paket_penerbitan::all()->random()->id,
            'buku_permohonan_terbit_id' => buku_permohonan_terbit::all()->random()->id,
            'no_transaksi' => "T" . $this->faker->randomNumber(1, 1000),
            'total_harga' => $this->faker->numberBetween(10000, 1000000),
            'status' => $status,
            'date_time_exp' => null,
            'dp_upload' => '/cover_buku.png',
            'pelunasan_upload' => '/cover_buku.png',
            'date_time_dp_lunas' => $date_time_dp_lunas,
            'date_time_lunas' => $date_time_lunas,
            'note' => $this->faker->randomElement([$this->faker->paragraph(), null]),
        ];
    }
}
