<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bus;
use Faker\Generator as Faker;

$factory->define(Bus::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\Fakecar($faker));
    return [
        'placabus'  => $faker->vehicleRegistration,
        'id_tipo_bus' => App\BusType::all()[0]->id_tipo_bus,
        'activo_bus'=> 'a'
    ];
});
