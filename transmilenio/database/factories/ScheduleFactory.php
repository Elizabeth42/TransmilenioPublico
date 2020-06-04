<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Schedule;
use Carbon\Carbon;
use Faker\Generator as Faker;


$factory->define(Schedule::class, function (Faker $faker) {
    $min_start_hour = 3; // osea desde la 3 a.m
    $max_start_hour = 17; // osea hasta las 5 p.m.
    $max_hours_travel = 18;
    $start = rand($min_start_hour*3600,$max_start_hour*3600);
    return [
        'horario_inicio' => date('Y-m-d H:i:s', $start),
        'horario_fin'=> date('Y-m-d H:i:s', rand($start+3600, $start+$max_hours_travel*3600)),
        'dia'=>$faker->dayOfWeek($max = 'Sunday'),
        'activo_horario'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
