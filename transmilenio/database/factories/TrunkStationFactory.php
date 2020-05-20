<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TrunkStation;
use Faker\Generator as Faker;

$factory->define(TrunkStation::class, function (Faker $faker) {
    $trunk = App\Trunk::all();
    $station = App\Station::all();
    return [
        'id_troncal' => $trunk->random()->id_troncal,
        'id_estacion' => $station->random()->id_estacion,
        'activo_troncal_estacion'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
