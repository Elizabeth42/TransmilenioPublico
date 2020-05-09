<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BusType;
use Faker\Generator as Faker;

$factory->define(BusType::class, function (Faker $faker) {
    return [
        'nombre_tipo' => $faker->text($maxNbChars = 50),
        'color'=>$faker->hexColor,
        'activo_tipo_bus'=> 'a'
    ];
});
