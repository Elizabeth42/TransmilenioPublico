<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Portal;
use Faker\Generator as Faker;

$factory->define(Portal::class, function (Faker $faker) {

    return [
        'nombre_portal' => $faker->text($maxNbChars = 50),
//        'id_troncal' => 1
    ];

});
//oli jajajaj
