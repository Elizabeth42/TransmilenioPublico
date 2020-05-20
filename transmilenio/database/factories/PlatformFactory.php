<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Platform;
use Faker\Generator as Faker;

$factory->define(Platform::class, function (Faker $faker) {
    $portal = App\Portal::all();
    return [
        'id_portal' =>  $portal->random()->id_portal,
        'numero_plataforma' => $faker->numberBetween(1,99),
        'activo_plataforma'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
