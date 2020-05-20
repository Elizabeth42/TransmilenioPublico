<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Schedule;
use Carbon\Carbon;
use Faker\Generator as Faker;


$factory->define(Schedule::class, function (Faker $faker) {
    return [
        //'horario_inicio' => Carbon::parse($faker->time($format='H:i:s', $max = '08:00:00')),
        'horario_inicio' => Carbon::createFromFormat('H:i:s', $faker->time($format='H:i:s', $max = '08:00:00')),
        'horario_fin'=> Carbon::parse($faker->time($format='H:i:s', $max = '23:59:59')),
        'dia'=>$faker->dayOfWeek($max = 'Sunday'),
        'activo_horario'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
