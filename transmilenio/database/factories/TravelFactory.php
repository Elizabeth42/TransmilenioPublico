<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Travel;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Travel::class, function (Faker $faker) {
    $asigment = App\TimeRouteAssignment::all();
    $dt = $faker->dateTimeBetween($startDate = '-2 years', $endDate = '-10 days');
    return [
        'id_asignacion_ruta'=> $asigment->random()->id_asignacion_ruta,
 //       'fecha_inicio_viaje' => Carbon::createFromFormat('d-m-Y H:i:s', $faker->time($format='H:i:s', $max = '08:00:00')),
        'fecha_inicio_viaje' =>  $dt->format("Y/m/d H:i:s"),
        'fecha_fin_viaje'=> null
    ];
});
