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
        factory(App\Portal::class, 10)->create()->each(function($portal) use ($trunks){
            $random = $trunks->random();
            $r = rand(0, 1);
            // permitira establecer si sera un portal activo o uno inactivo
            if($r==0){
                $portal->activo_portal = 'n';
            }else{
                $portal->activo_portal = 'a';
            }
            // permitira validar si la troncal no se encuentre activa
            if ($random->activo_troncal != 'n') {
                $portal->id_troncal = $random->id_troncal;
                $portal->save();
            }else{
                $portal->delete(); // si la troncal se encuentra inactiva se borra el registro de la plataforma creada
            }
        });
    }
}
