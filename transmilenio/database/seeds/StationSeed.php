<?php

use Illuminate\Database\Seeder;

class StationSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Station::class, 7)->create()->each(function ($station) {
           $station->save();

            //   $illness->vaccines()->attach([10, 11]);
        });
        // esto es para generar los aleatorios entre troncal y estacion
        $trunks = App\Trunk::all();
        $stations = App\Station::all();
        for ($i = 1; $i <= 7; $i++) {
            $randomS = $stations->random();
            $randomT = $trunks->random();
            $randomT->stations()->attach($randomS->id_estacion);
        }

    }
}
