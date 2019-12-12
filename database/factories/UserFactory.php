<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => substr($faker->userName, 0, 15),
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'display_name' => substr($faker->name, 0, 20),
        'is_protected' => false,
        'accept_analytics' => false,
        'private_likes' => false,
    ];
});

$factory->state(App\User::class, 'protected', [
    'is_protected' => true,
]);
