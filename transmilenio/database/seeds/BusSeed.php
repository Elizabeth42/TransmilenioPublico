<?php

use Illuminate\Database\Seeder;

class BusSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $busTypes = App\BusType::all();
        factory(App\Bus::class, 10)->create()->each(function($bus) use ($busTypes){
            $enable = rand(0,1);
            $stateBus = $enable == 0 ? 'n' : 'a';
            $bus->activo_bus = $stateBus;

            $random = $busTypes->random();
            // permitira validar si la el tipo de bus se encuentra activa
            if ($random->activo_tipo_bus != 'n') {
                $bus->id_tipo_bus = $random->id_tipo_bus;
                $bus->save();
            }else{
                $bus->delete(); // si el tipo de bus se encuentra inactivo se borra el registro del bus creado
            }
        });
    }
}
