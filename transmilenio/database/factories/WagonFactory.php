<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Wagon;
use Faker\Generator as Faker;

$factory->define(Wagon::class, function (Faker $faker) {
    return [
        'numero_vagon' => $faker->numberBetween(1,99),
        'id_plataforma' => App\Platform::all()[0]->id_plataforma
    ];
});
