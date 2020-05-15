<?php

use Illuminate\Database\Seeder;

class TravelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $asigments = App\TimeRouteAssignment::all();
        factory(App\Travel::class, 10)->create()->each(function($travel) use ($asigments){
            $randomA = $asigments->random();
            // permitira validar si la troncal no se encuentre activa
            Log::info('--------------------------------------------------------------------------------------');
            Log::info('id asignacion: '.$randomA->id_asignacion_ruta);
            if ($randomA->activo_asignacion != 'n') {
                Log::info('se encuentra activa');
                // para validar que no se encuentren asociados ya (fecha inicio y el id de asignacion ruta)
                $exist =App\Travel::where('id_asignacion_ruta','=',$randomA->id_asignacion_ruta)->where('fecha_inicio_viaje','=',$travel->fecha_inicio_operacion)->count();
                Log::info('segundo if: '.$exist);
                if($exist==0){
                    $travel->id_asignacion_ruta = $randomA->id_asignacion_ruta;
                    $travel->save();
                }else{
                    $travel->delete(); // si el id de la asignacion y la fecga de inicio coincide se borra el registro del viaje creado
                }
            }else{
                $travel->delete(); // si la asignacion se encuentra inactiva se borra el registro del viaje creado
            }
        });
    }
}
