<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class WagonSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $platforms = App\Platform::all();
        if ($platforms->count() > 0) {
            factory(App\Wagon::class, 3)->create()->each(function($wagon) use ($platforms){
                $random = $platforms->random();
                $wagon->id_plataforma = $random->id_plataforma;
                $wagon->save();
            });
        }else{
            Log::info(print_r('no se puede insertar vagones si no hay plataformas previamente creadas'));
        }
        // [1: {"orden": 1, "estado_parada": "gdhghgfj"}, 3: {"orden": 1, "estado_parada": "gdhghgfj"} ]
    }
}
