<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Wagon;
use Faker\Generator as Faker;

$factory->define(Wagon::class, function (Faker $faker) {
    $id =rand(0,1) == 0 ? 'p' :'t';
   if ($id=='p'){
       $plataforma = App\Platform::all();
       return [
           'id_plataforma'=>$plataforma->random()->id_plataforma,
           'numero_vagon' => $faker->numberBetween(1,99),
           'activo_vagon'=> rand(0, 1) == 0 ? 'n' : 'a'

       ];
   }else{
       $trunk_Station = App\TrunkStation::all();
       return [
           'id_troncal_estacion'=>$trunk_Station->random()->id_troncal_estacion,
           'numero_vagon' => $faker->numberBetween(1,99),
           'activo_vagon'=> rand(0, 1) == 0 ? 'n' : 'a'
       ];
   }
});
