<?php

namespace Database\Factories;

use App\Models\buku_dijual;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\storage_buku_dijual>
 */
class storage_buku_dijualFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipes = $this->faker->randomElement(["IMAGE", "PDF"]);

        if ($tipes == "IMAGE") {
            $nama_file = $this->faker->randomElement([$this->faker->word() . ".jpg", $this->faker->word() . ".png"]);
        } else {
            $nama_file = $this->faker->randomElement([$this->faker->word() . ".pdf"]);
        }

        // generate name
        $nama_generate = buku_dijual::all()->random()->judul . "_" . $this->faker->word() . "." . $this->faker->fileExtension();

        return [
            'buku_dijual_id' => buku_dijual::all()->random()->id,
            'tipe' => $tipes,
            'nama_file' => $nama_file,
            'nama_generate' => $nama_generate,
        ];
    }
}
