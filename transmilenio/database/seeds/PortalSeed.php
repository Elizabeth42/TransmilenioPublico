<?php

use Illuminate\Database\Seeder;

class PortalSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $trunks = App\Trunk::all();
        factory(App\Portal::class, 10)->create()->each(function($trunk) use ($trunks){
            $random = $trunks->random();
            $trunk->id_troncal = $random->id_troncal;
            $r =  rand(0, 1);
            if ($r == 0) { // se asumira que es inactiva
                $trunk->activo_portal = 'n';
                $trunk->save();
            }else{
                $trunk->save();
            }
            $trunk->save();
        });
    }
}
