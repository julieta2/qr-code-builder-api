<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class QRCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'content' => fake()->text(),
            'size' => fake()->numerify,
            'background_color' => fake()->rgbColor,
            'fill_color' => fake()->rgbColor,
            "image" => fake()->text
        ];
    }
}
