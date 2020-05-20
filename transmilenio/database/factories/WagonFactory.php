<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Wagon;
use Faker\Generator as Faker;

$factory->define(Wagon::class, function (Faker $faker) {
    return [
        'numero_vagon' => $faker->numberBetween(1,99),
        'activo_vagon'=> rand(0, 1) == 0 ? 'n' : 'a'
    ];
});
