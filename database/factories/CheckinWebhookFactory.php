<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\CheckinWebhook;
use Faker\Generator as Faker;

$factory->define(CheckinWebhook::class, function (Faker $faker) {
    return [
        'name' => 'example'
    ];
});
