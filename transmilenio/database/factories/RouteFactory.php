<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Route;
use Faker\Generator as Faker;

$factory->define(Route::class, function (Faker $faker) {
    return [
        'codigo_ruta' => $faker->numberBetween(1,999)
    ];
});
