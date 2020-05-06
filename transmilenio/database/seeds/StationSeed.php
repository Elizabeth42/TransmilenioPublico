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
        factory(App\Station::class, 3)->create()->each(function ($station) {
            $r =  rand(0, 1);
            if($r==0) {
                $station->activo_estacion = 'n';
                $station->save();
            }else{
                $station->save();
            }
        });
    }
}
