<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TimeRouteAssignment;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(TimeRouteAssignment::class, function (Faker $faker) {
    $routes = App\Route::all();
    $buses = App\Bus::all();
    $schedule = App\Schedule::all();
    $dt = $faker->dateTimeBetween($startDate = '-19 years', $endDate = '-10 days');
    return [
        'id_ruta' => $routes->count() > 0 ? $routes->random()->id_ruta: -1,
        'id_bus' => $buses->count() > 0 ? $buses->random()->id_bus: -1,
        'id_horario' => $schedule->count() > 0 ? $schedule->random()->id_horario: -1,
        'fecha_inicio_operacion' => $dt->format("Y-m-d"),
        //'fecha_inicio_operacion' => Carbon::createFromFormat('Y-m-d',$faker->dateTimeBetween('-19 year', '-10 days')),
        'activo_asignacion'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
