<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\User>
 */
class UserFactory extends Factory
{
    private static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => substr($this->faker->userName, 0, 15),
            'email' => $this->faker->unique()->safeEmail,
            'password' => self::$password ?: self::$password = bcrypt('secret'),
            'remember_token' => Str::random(10),
            'display_name' => substr($this->faker->name, 0, 20),
            'is_protected' => false,
            'accept_analytics' => false,
            'private_likes' => false,
        ];
    }

    public function protected(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_protected' => true,
        ]);
    }
}
