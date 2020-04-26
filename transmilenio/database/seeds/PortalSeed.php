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
        factory(App\Portal::class, 3)->create()->each(function($trunk) use ($trunks){
            $random = $trunks->random();
            $trunk->id_troncal = $random->id_troncal;
            $trunk->save();
        });
    }
}
