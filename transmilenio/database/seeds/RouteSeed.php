<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class RouteSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Route::class, 3)->create();
        // esto es para generar los aleatorios de parada
        $wagons = App\Wagon::all();
        $routes = App\Route::all();
        for ($i = 1; $i <= 20; $i++) {
            $randomW = $wagons->random();
            $randomR = $routes->random();
            $stop = self::validate($randomW, $randomR);
            if(isset($stop))
                $randomR->wagons()->attach($stop['id_vagon'], ['estado_parada' => $stop['estado_parada'], 'orden'=>$stop['orden'] ]);
        }
    }

    public static function validate($randomW, $randomR){
        //permitira validar que el vagon y la ruta esten activas, ademass que la ruta no tenga ya asignado ese vagon
        if ($randomW->activo_vagon=='a' && $randomR->activo_ruta == 'a'&& !$randomR->hasWagon($randomW->id_vagon)) {
            // si ya hay vagones asociados a esa ruta verifique cual es el ultimo asignado
            self::addStop($randomW,$randomR);
        }
        return null;
    }

    public static function addStop($randomW, $randomR){
        if ($randomR->wagons()->count() > 0)
            $last_bus_stop = $randomR->wagons()->withPivot('orden')->orderBy('orden', 'DESC')->first();
        Log::info('el valor de vagon: '.$randomW->id_vagon);
        Log::info('el valor de ruta: '. $randomR->id_ruta);
        return [
            'id_vagon' => $randomW->id_vagon,
            'id_ruta' => $randomR->id_ruta,
            'estado_parada' => rand(0, 1) == 0 ? 'n' : 'a',
            'orden' => isset($last_bus_stop) ? $last_bus_stop->pivot->orden + 1 : 1
        ];
    }
}
