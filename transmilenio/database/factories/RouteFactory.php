<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Route;
use Faker\Generator as Faker;

$factory->define(Route::class, function (Faker $faker) {
    return [
        'codigo_ruta' => $faker->text($maxNbChars = 5),
        'activo_ruta'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
