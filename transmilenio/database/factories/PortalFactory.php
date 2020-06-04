<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Portal;
use Faker\Generator as Faker;

$factory->define(Portal::class, function (Faker $faker) {
    $trunk = App\Trunk::all();
    return [
        'nombre_portal' => $faker->realText(rand(10,50)),
        'id_troncal' => $trunk->random()->id_troncal,
        'activo_portal'=> rand(0,1) == 0 ? 'n' : 'a'
    ];

});
