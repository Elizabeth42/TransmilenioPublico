<?php

use Illuminate\Database\Seeder;

class TimeRouteAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    //
    public function run()
    {
        $buses = App\Bus::all();
        $rutas = App\Route::all();
        $horarios = App\Schedule::all();
        factory(App\TimeRouteAssignment::class, 5)->create()->each(function ($assigment) use ($buses,$rutas,$horarios) {
            $enable = rand(0,1);
            $stateAssigment = $enable == 0 ? 'n' : 'a';
            $assigment->activo_asignacion = $stateAssigment;

            $randomB = $buses->random();
            $randomR = $rutas->random();
            $randomH = $horarios->random();

            // ahora es necesario validar que el bus, la ruta y el horario que se van a asignar se encuentren activos
            if ($randomB->activo_bus != 'n' && $randomR->activo_ruta != 'n' && $randomH->activo_horario !='n'){
                // para validar que no se encuentren asociados ya (ruta, horario, bus, fecha inicia son unicos)
                if (App\TimeRouteAssignment:: where('ID_BUS', '=', $randomB->id_bus)->where('ID_RUTA', '=', $randomR->id_ruta)->where('ID_HORARIO', '=', $randomH->id_horario)->where('FECHA_INICIO_OPERACION', '=', $assigment->fecha_inicio_operacion)->count() == 0) {
                    $assigment->id_ruta = $randomR->id_ruta;
                    $assigment->id_bus = $randomB->id_bus;
                    $assigment->id_horario = $randomH->id_horario;
                    $assigment->save();
                }else{
                    $assigment->delete();
                }
            }else{
                $assigment->delete();
            }
        });
    }
}
