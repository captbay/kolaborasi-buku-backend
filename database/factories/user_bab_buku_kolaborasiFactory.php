<?php

namespace Database\Factories;

use App\Models\bab_buku_kolaborasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\user_bab_buku_kolaborasi>
 */
class user_bab_buku_kolaborasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::where('role', '!=', 'ADMIN')->inRandomOrder()->first()->id,
            'bab_buku_kolaborasi_id' => bab_buku_kolaborasi::all()->random()->id,
            'status' => $this->faker->randomElement(['DONE', 'PROGRESS', 'REVISI', 'REJECTED', 'UPLOADED']),
            'note' => $this->faker->randomElement([$this->faker->paragraph(), null]),
            'file_bab' => '/buku_final_temp.pdf',
            'datetime_deadline' => $this->faker->dateTimeBetween('now', '+1 years'),
        ];
    }
}
