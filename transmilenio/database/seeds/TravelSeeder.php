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
        factory(App\Travel::class, 10)->make()->each(function($travel) {
            $valid = self::validate($travel);
            if($valid)
                $travel->save();
        });
    }

    public static function validate($travel){
        if ($travel->asinnations()->first()->activo_asignacion != 'n') {
            if(App\Travel::where('id_asignacion_ruta','=',$travel->asinnations()->first()->id_asignacion_ruta)->where('fecha_inicio_viaje','=',$travel->fecha_inicio_operacion)->count()==0){
                return true;
            }
        }
        return false;
    }
}
