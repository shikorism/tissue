<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ContentProvider;
use Faker\Generator as Faker;

$factory->define(ContentProvider::class, function (Faker $faker) {
    return [
        'host' => 'example.com',
        'robots' => null,
        'robots_cached_at' => now(),
    ];
});
