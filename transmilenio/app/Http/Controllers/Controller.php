<?php

namespace App\Http\Controllers;

// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Route;

class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;


    /*estas rutas seran para descargar los datos desde la base de datos
    lo unico es pasarle los modelos por parametro, por ejemplo
    http://localhost:8000/download/route
    http://localhost:8000/download/wagon
    http://localhost:8000/download/stop
    http://localhost:8000/download/trunk
    */
    public function download($model)
    {
        if ($model == 'stop') {
            $content = collect();
            foreach (\App\Route::all() as $route)
                $content->add([$route->getKey() => $route->wagons()->withPivot('estado_parada', 'orden')->get()]);
        } else {
            //Str::studly permite convertir mayusculas en minusculas para evitar errores
            $instance = '\\App\\' . \Illuminate\Support\Str::studly($model);
            $content = $instance::all();
        }
        return response($content)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Content-disposition' => 'attachment; filename=' . $model . 's.json'
            ]);
    }
}
