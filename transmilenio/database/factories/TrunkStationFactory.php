<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TrunkStation;
use Faker\Generator as Faker;

$factory->define(TrunkStation::class, function (Faker $faker) {
    return [
        'activo_troncal_estacion'=> 'a',
        'id_troncal' => 1,
        'id_estacion' => 1,
    ];
});
