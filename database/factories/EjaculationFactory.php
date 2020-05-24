<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Ejaculation;
use Faker\Generator as Faker;

$factory->define(Ejaculation::class, function (Faker $faker) {
    return [
        'ejaculated_date' => $faker->date('Y-m-d H:i:s'),
        'note' => $faker->text,
        'source' => Ejaculation::SOURCE_WEB,
    ];
});
