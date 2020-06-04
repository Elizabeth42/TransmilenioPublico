<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Trunk;
use Faker\Generator as Faker;

$factory->define(Trunk::class, function (Faker $faker) {
    return [
        'nombre_troncal'=> $faker->realText(rand(10,50)),
        'letra_troncal'=> $faker->lexify('??'),
        'color_troncal'=> $faker->hexColor,
        'activo_troncal'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
