<?php

use Illuminate\Database\Seeder;

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
        for ($i = 1; $i <= 7; $i++) {
            $randomS = $stations->random();
            $randomT = $trunks->random();
            $trunkStation = new App\TrunkStation();
            if(App\TrunkStation:: where ('ID_ESTACION','=',$randomS->id_estacion)->where('ID_TRONCAL','=',$randomT->id_troncal)->count () == 0) {
                $trunkStation->id_estacion = $randomS->id_estacion;
                $trunkStation->id_troncal = $randomT->id_troncal;
                $trunkStation->activo_troncal_estacion = 'a';
                $trunkStation->save();
            }
    }
}
