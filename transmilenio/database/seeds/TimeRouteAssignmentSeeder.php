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
            factory(App\TimeRouteAssignment::class, 3)->make()->each(function($asigment) {
                $valid = self::validate($asigment);
                if($valid)
                    $asigment->save();
            });

    }

    public static function validate($asigment){
        if ($asigment->schedules()->first()->activo_horario != 'n'||$asigment->buses()->first()->activo_bus != 'n'||$asigment->routes()->first()->activo_ruta != 'n') {
            if (\App\TimeRouteAssignment:: where('ID_BUS', '=', $asigment->buses()->first()->id_bus)->where('ID_RUTA', '=', $asigment->routes()->first()->id_ruta)->where('ID_HORARIO', '=', $asigment->schedules()->first()->id_horario)->where('FECHA_INICIO_OPERACION', '=', $asigment->fecha_inicio_operacion)->count() == 0) {
                return true;
            }
        }
        return false;
    }
}
