<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class TrunkStationSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // esto es para generar los aleatorios entre troncal y estacion
        $trunks = App\Trunk::all();
        $stations = App\Station::all();
        for ($i = 1; $i <= 5; $i++) {
            $randomS = $stations->random();
            $randomT = $trunks->random();
            $trunkStation = new App\TrunkStation();
            $r = rand(0, 1);
            if($r==0){
                $trunkStation->activo_troncal_estacion = 'n';
            }else{
                $trunkStation->activo_troncal_estacion = 'a';
            }
            // permitira validar si la estacion o la troncal no se encuentre activa
            if ($randomS->activo_estacion != 'n' && $randomT->activo_troncal != 'n'){
                // para validar que no se encuentren asociados ya
                if (App\TrunkStation:: where('ID_ESTACION', '=', $randomS->id_estacion)->where('ID_TRONCAL', '=', $randomT->id_troncal)->count() == 0) {
                    $trunkStation->id_estacion = $randomS->id_estacion;
                    $trunkStation->id_troncal = $randomT->id_troncal;
                    $trunkStation->save();
                }
            }
        }
    }
}
