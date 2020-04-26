<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Trunk;
use Faker\Generator as Faker;

$factory->define(Trunk::class, function (Faker $faker) {
    return [
        'nombre_troncal' => $faker->text($maxNbChars = 50),
        'letra_troncal'=> $faker->lexify('??'),
        'color_troncal'=>$faker->hexColor
    ];
});
