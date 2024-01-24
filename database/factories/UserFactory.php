<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'no_telepon' => $this->faker->phoneNumber(),
            'tgl_lahir' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['Pria', 'Wanita']), // 'L' or 'P
            'alamat' => $this->faker->address(),
            'provinsi' => $this->faker->state(),
            'kecamatan' => $this->faker->city(),
            'kota' => $this->faker->city(),
            'kode_pos' => $this->faker->postcode(),
            'foto_profil' => $this->faker->imageUrl(),
            'bio' => $this->faker->text(),
            'kode_verif_email' => $this->faker->randomNumber(),
            'status_verif_email' => $this->faker->randomElement([0, 1]), // 0 or 1
            'email_verified_at' => now(),
            'file_cv' => $this->faker->imageUrl(),
            'file_ktp' => $this->faker->imageUrl(),
            'file_ttd' => $this->faker->imageUrl(),
            'role' => $this->faker->randomElement(['ADMIN', 'CUSTOMER', 'MEMBER']), // 'ADMIN', 'CUSTOMER', 'MEMBER'
            'active_flag' => $this->faker->randomElement([0, 1]), // '1', '0
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
