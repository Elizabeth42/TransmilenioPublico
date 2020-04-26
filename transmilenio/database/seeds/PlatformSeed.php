<?php

use Illuminate\Database\Seeder;

class PlatformSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $portals = App\Portal::all();
        $stations = App\Station::all();
        factory(App\Platform::class, 10)->create()->each(function ($platform) use ($portals, $stations) {
            $r =  rand(0, 1);
            if ($r == 0) // se asumira que es asociada a un portal
            {
                $random = $portals->random();
                $platform->id_portal = $random->id_portal;
                $platform->save();
            } else {
                $random = $stations->random();
                $trunks = $random->trunks()->get();
                if($trunks->Count() > 0) {
                    $platform->id_estacion = $random->id_estacion;
                    $platform->id_troncal = $trunks->random()->id_troncal;
                    $platform->numero_plataforma=1;
                    $platform->save();
                }
            }
        });

    }
}
