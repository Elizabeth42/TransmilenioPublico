<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Station;
use Faker\Generator as Faker;

$factory->define(Station::class, function (Faker $faker) {
    return [
        'nombre_estacion' => $faker->text($maxNbChars = 50),
        'activo_estacion'=> 'a'
    ];
});
