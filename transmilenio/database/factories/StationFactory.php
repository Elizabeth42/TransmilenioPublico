<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Station;
use Faker\Generator as Faker;

$factory->define(Station::class, function (Faker $faker) {
    return [
        'nombre_estacion' => $faker->realText(rand(10,50)),
        'activo_estacion'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
