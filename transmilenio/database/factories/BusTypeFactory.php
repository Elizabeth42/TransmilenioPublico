<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BusType;
use Faker\Generator as Faker;

$factory->define(BusType::class, function (Faker $faker) {
    return [
        'nombre_tipo' => $faker->realText(rand(10,50)),
        'color'=>$faker->hexColor,
        'activo_tipo_bus'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
