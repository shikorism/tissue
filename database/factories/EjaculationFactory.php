<?php

namespace Database\Factories;

use App\Ejaculation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Ejaculation>
 */
class EjaculationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ejaculated_date' => $this->faker->date('Y-m-d H:i:s'),
            'note' => $this->faker->text,
            'source' => Ejaculation::SOURCE_WEB,
        ];
    }
}
