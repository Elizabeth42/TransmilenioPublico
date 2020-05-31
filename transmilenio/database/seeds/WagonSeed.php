<?php

use App\Platform;
use App\TrunkStation;
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
            $valid = $this->validate($wagon);
            if($valid)
                $wagon->save();
        });
    }

    public static function validate($wagon){
        // permitira validar si la plataforma  se encuentre activa
        if($wagon->id_plataforma !=null){
            $plataforma = Platform::find($wagon->id_plataforma);
            if ($wagon->platform()->first()->activo_plataforma!= 'n') {
                //finalmente se requiere garantizar que esa plataforma no tenga asignada ya este numero de vagon
                if (!$plataforma->hasNumberWagon($wagon->numero_vagon)){
                    return true;
                }
            }
        }else{
            $troncalEstacion = TrunkStation::find($wagon->id_troncal_estacion);
            if ($wagon->trunk_station()->first()->activo_troncal_estacion != 'n') {
                //finalmente se requiere garantizar que esa troncal_estacion no tenga asignada ya este numero de vagon
                if (!$troncalEstacion->hasNumberWagon($wagon->numero_vagon)){
                    return true;
                }
            }
        }
        return false;
    }
}
