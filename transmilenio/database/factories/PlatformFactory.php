<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Platform;
use Faker\Generator as Faker;

$factory->define(Platform::class, function (Faker $faker) {
    return [
        'numero_plataforma' => $faker->numberBetween(1,99),
        'activo_plataforma'=> 'a'
    ];
});
