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
        factory(App\TrunkStation::class, 10)->make()->each(function($trunkStation) {
            $valid = self::validate($trunkStation);
            if($valid)
                $trunkStation->save();
        });
    }

    public static function validate($trunkStation){
        // debe validar que tanto la troncal como la estacion designadas se encuentren activas
        if ($trunkStation->trunk()->first()->activo_troncal != 'n' && $trunkStation->station()->first()->activo_estacion != 'n') {
            // para validar que no se encuentren asociados ya
            if (\App\TrunkStation:: where('ID_ESTACION', '=', $trunkStation->station()->first()->id_estacion)->
                where('ID_TRONCAL', '=',$trunkStation->trunk()->first()->id_troncal)->count() == 0) {
                return true;
            }
        }
        return false;
    }
}
