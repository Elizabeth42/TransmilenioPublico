<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TimeRouteAssignment;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(TimeRouteAssignment::class, function (Faker $faker) {
    return [
        'id_ruta' => App\Route::all()[0]->id_ruta,
        'id_bus' => App\Bus::all()[0]->id_bus,
        'id_horario' => App\Schedule::all()[0]->id_horario,
        'fecha_inicio_operacion' => $faker->dateTimeBetween('-19 year', '-10 days'),
        //'fecha_inicio_operacion' => Carbon::createFromFormat('Y-m-d',$faker->dateTimeBetween('-19 year', '-10 days')),
        'activo_asignacion'=> 'a'
    ];
});
