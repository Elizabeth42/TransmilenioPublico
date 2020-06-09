<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class TravelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Travel::class, 120)->make()->each(function($travel) {
            $valid = self::validate($travel);
            if($valid)
                $travel->save();
        });
    }

    public static function validate($travel){
        // permite validar que la asignacion se encuentre activa
        if ($travel->asinnations()->first()->activo_asignacion != 'n') {
            //sirve para validar que el id de la asignacion y la fecha de inicio sean unicas
            if(App\Travel::where('id_asignacion_ruta','=',$travel->asinnations()->first()->id_asignacion_ruta)
                    ->where('fecha_inicio_viaje','=',$travel->fecha_inicio_operacion)->count()==0){
               // ahora es necesario confirmar que la fecha de inicio de operacion
                // en la tabla asignacion sea menor a la fecha de inicio viaje de la tabla viaje
                $validateDate = Carbon::parse($travel->asinnations()->first()->fecha_inicio_operacion)->gt($travel->fecha_inicio_viaje);
                if(!$validateDate){
                    return true;
                }
            }
        }
        return false;
    }
}
