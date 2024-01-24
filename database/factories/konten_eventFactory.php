<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\konten_event>
 */
class konten_eventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipes = $this->faker->randomElement(["IMAGE", "VIDEO"]);

        if ($tipes == "IMAGE") {
            $files = $this->faker->randomElement([$this->faker->word() . ".jpg", $this->faker->word() . ".png"]);
        } else {
            $files = $this->faker->randomElement([$this->faker->word() . ".mp4"]);
        }
        return [
            'tipe' => $tipes,
            'file' => $files,
            'waktu_mulai' => $this->faker->dateTime(),
            'waktu_selesai' => $this->faker->dateTime(),
        ];
    }
}
