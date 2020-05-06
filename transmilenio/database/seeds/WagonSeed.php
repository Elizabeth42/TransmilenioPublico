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
        $troncalStation = App\TrunkStation::all();
            factory(App\Wagon::class, 10)->create()->each(function($wagon) use ($platforms,$troncalStation){
                $randomP = $platforms->random();
                $randomT = $troncalStation->random();
                $rEnable = rand(0, 1);
                // permitira establecer si sera un vagon esta activo o uno inactivo
                if($rEnable==0){
                    $wagon->activo_vagon = 'n';
                }else{
                    $wagon->activo_vagon = 'a';
                }
                // validara si el vagon se encuentra asociado a una plataforma o a una troncal_estacion
                $r =  rand(0, 1);
                if ($r == 0) // se asumira que es asociada a una plataforma
                {
                    // permitira validar si la plataforma  se encuentre activa
                    if ($randomP->activo_plataforma != 'n') {
                        $wagon->id_plataforma = $randomP->id_plataforma;
                        $wagon->save();
                    }else{
                        $wagon->delete(); // si la plataforma se encuentra inactiva se borra el registro del vagon creado
                    }
                } else { // se asumira que es asociada a una troncal_estacion
                    // permitira validar si la troncal_estacion  se encuentre activa
                    if ($randomT->activo_troncal_estacion != 'n') {
                        $wagon->id_troncal_estacion = $randomT->id_troncal_estacion;
                        $wagon->save();
                    }else{
                        $wagon->delete(); // si la troncal_Estacion se encuentra inactiva se borra el registro del vagon creado
                    }
                }
            });
    }
}
