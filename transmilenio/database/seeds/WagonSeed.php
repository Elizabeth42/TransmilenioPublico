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
        factory(App\Wagon::class, 10)->make()->each(function($wagon) {
            $valid = self::validate($wagon);
            if($valid)
                $wagon->save();
        });
    }

    public static function validate($wagon){
        // validara si el vagon se encuentra asociado a una plataforma o a una troncal_estacion
        $r =  rand(0, 1);
        if ($r == 0) // se asumira que es asociada a una plataforma
        {
            $randomP = App\Platform::all()->random();
            // permitira validar si la plataforma  se encuentre activa
            if ($randomP->activo_plataforma != 'n') {
                $wagon->id_plataforma = $randomP->id_plataforma;
                return true;
            }
        } else { // se asumira que es asociada a una troncal_estacion
            $randomT = App\TrunkStation::all()->random();
            // permitira validar si la troncal_estacion  se encuentre activa
            if ($randomT->activo_troncal_estacion != 'n') {
                $wagon->id_troncal_estacion = $randomT->id_troncal_estacion;
                return true;
            }
        }
        return false;
    }
}
