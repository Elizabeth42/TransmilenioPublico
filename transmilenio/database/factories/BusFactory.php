<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bus;
use Faker\Generator as Faker;

$factory->define(Bus::class, function (Faker $faker) {
    $types = App\BusType::all();
    $faker->addProvider(new \Faker\Provider\Fakecar($faker));
    return [
        'placabus'  => $faker->vehicleRegistration,
        'id_tipo_bus' => $types->random()->id_tipo_bus,
        'activo_bus'=> rand(0,1) == 0 ? 'n' : 'a'
    ];
});
