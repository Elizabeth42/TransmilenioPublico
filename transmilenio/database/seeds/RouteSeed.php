<?php

use Illuminate\Database\Seeder;

class RouteSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Route::class, 10)->create()->each(function ($route) {
            $r =  rand(0, 1);
            if($r==0) {
                $route->activo_ruta = 'n';
                $route->save();
            }else{
                $route->save();
            }
            $route->save();
        });
        // esto es para generar los aleatorios de parada
        $wagons = App\Wagon::all();
        $routes = App\Route::all();
        Log::info('--------------------------------------------------------------------------------------------------------------------------------------');
        for ($i = 1; $i <= 20; $i++) {
            $randomW = $wagons->random();
            $randomR = $routes->random();
            // valida que el vagon, la ruta esten activas y que el vagon no tenga asignada la ruta
            if ($randomW->activo_vagon=='a' && $randomR->activo_ruta = 'a'&& !$randomR->hasWagon($randomW->id_vagon)){
                $enable = rand(0,1);
                $state_parada = $enable == 0 ? 'n' : 'a';
                $last_bus_stop = null;
                // si ya hay vagones asociados a esa ruta verifique cual es el ultimo asignado
                if($randomR->wagons()->count() > 0)
                    $last_bus_stop = $randomR->wagons()->withPivot('orden')->orderBy('orden', 'DESC')->first();
                if (isset($last_bus_stop)){//esto es para verificar si existe una ultima parada para asignar una nueva o crear la primera
                    $randomR->wagons()->attach($randomW->id_vagon,['estado_parada'=>$state_parada,'orden'=> $last_bus_stop->pivot->orden+1]);
                }else{
                    $randomR->wagons()->attach($randomW->id_vagon,['estado_parada'=>$state_parada,'orden'=> 1]);
                }
            }
        }
    }
}
