<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Wagon;
use Faker\Generator as Faker;

$factory->define(Wagon::class, function (Faker $faker) {
    $wagon = [
        'numero_vagon' => $faker->numberBetween(1,99),
        'activo_vagon'=> rand(0, 1) == 0 ? 'n' : 'a'
    ];
    $type =rand(0,1); // type 0 platform 1 troncal station
    if ($type==0)
        $wagon['id_plataforma']=App\Platform::all()->random()->getKey();
   else
       $wagon['id_troncal_estacion']=App\TrunkStation::all()->random()->getKey();
    return $wagon;
});
