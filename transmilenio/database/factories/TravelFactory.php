<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Travel;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Travel::class, function (Faker $faker) {
    $asigment = App\TimeRouteAssignment::all();
    return [
        'id_asignacion_ruta'=> $asigment->random()->id_asignacion_ruta,
 //       'fecha_inicio_viaje' => Carbon::createFromFormat('d-m-Y H:i:s', $faker->time($format='H:i:s', $max = '08:00:00')),
        'fecha_inicio_viaje' =>  $faker->dateTime($max = 'now', $timezone = null),
        'fecha_fin_viaje'=> null
    ];
});
