<?php

use App\Platform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

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
        factory(App\Platform::class, 5)->create()->each(function ($platform) use ($portals) {
            $random = $portals->random();
            $r = rand(0, 1);
            // permitira establecer si sera una plataforma esta activa o uno inactiva
            if($r==0){
                $platform->activo_plataforma = 'n';
            }else{
                $platform->activo_plataforma = 'a';
            }

            // permitira validar si el portal  se encuentre activa
            if ($random->activo_portal != 'n') {
                $platform->id_portal = $random->id_portal;
                $platform->save();
            }else{
                $platform->delete(); // si el portal se encuentra inactivo se borra el registro de la plataforma creada

            }
        });

    }
}
