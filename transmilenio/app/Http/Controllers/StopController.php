<?php

namespace App\Http\Controllers;

use App\Route;
use App\Wagon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StopController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function get_wagons_from_route($id)
    {
        $route = Route::find($id);
        if (!isset($route))
            return response('{"error": "La ruta no existe"}', 300)->header('Content-Type', 'application/json');
        if(request()->header('active')) {
            $active = request()->header('active');
            return response ($route->wagons()->withPivot('estado_parada','orden')->where('paradas.estado_parada', '=', $active)->get());
        }
        return response($route->wagons()->withPivot('estado_parada','orden')->get()->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * create new stoped
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function add_wagons_to_route(Request $request, $id)
    {
        $route = Route::find($id);
        Log::info('_____________________________________________________________________________________________');
        if (!isset($route))
            return response('{"error": "La ruta no existe"}', 300)->header('Content-Type', 'application/json');
        Log::info('La ruta existe');
        if ($route->activo_ruta == 'n')
            return response('{"error": "La ruta se encuentra inactiva"}', 300)->header('Content-Type', 'application/json');
        Log::info('La ruta esta activa');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $paradas = $validator->validated()['wagons'];
        Log::info('asignadas paradas');
        foreach ($paradas as $key => $parada) {
            $id_wagon = $parada['id_vagon'];
            $wagon = Wagon::find($id_wagon);
            // permitira establecer si la ruta ya tiene este vagon asociado y si el vagon se encuentra activo
            if($route->hasWagon($id_wagon))
                return response('{"error": "el vagon ya se encuentra asignado a esta ruta"}', 300)->header('Content-Type', 'application/json');
                //unset($paradas[$key]);
            // El ultimo vagon asignado a esa ruta
            $last_bus_stop = $route->wagons()->withPivot('orden')->orderBy('orden', 'DESC')->first();
            // basicamente se pregunta que si existe un ultomo vagon y a partir de ello se asigna el orden
            $orden = isset($last_bus_stop) ? $last_bus_stop->pivot->orden+1 : 1;
            $route->wagons()->attach($id_wagon,['estado_parada'=>$parada['estado_parada'],'orden'=> $orden]);
//            if (isset($last_bus_stop)){//verificar si existe un ultimo vagon asociado o sera el primero
//                $route->wagons()->attach($id_wagon,['estado_parada'=>$wagon->state_parada,'orden'=> $last_bus_stop->pivot->orden+1]);
//                return response('{"success": "Agregadas correctamente los vagones a la ruta"}', 200)->header('Content-Type', 'application/json');
//            }else{
//                $route->wagons()->attach($id_wagon,['estado_parada'=>$wagon->state_parada,'orden'=> 1]);
//                return response('{"success": "Agregadas correctamente los vagones a la ruta"}', 200)->header('Content-Type', 'application/json');
//            }
        }
        return response('{"success": "Agregadas correctamente los vagones a la ruta"}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function modify_wagons_to_route(Request $request, $id)
    {
        $route = Route::find($id);
        if (!isset($route))
            return response('{"error": "La ruta no existe"}', 300)->header('Content-Type', 'application/json');
        if ($route->activo_ruta == 'n')
            return response('{"error": "La ruta se encuentra inactiva"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $paradas = $validator->validated()['wagons'];
        $new_wagons = collect();
        foreach($paradas as $key => $wagon){
            $new_wagons[$wagon['id_vagon']] = collect();
            $new_wagons[$wagon['id_vagon']]['estado_parada'] = $wagon['estado_parada'];
            $new_wagons[$wagon['id_vagon']]['orden'] = $key+1;
        }
        $route->wagons()->sync($new_wagons);
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            [
                'wagons' => 'required|array',
                'wagons.*.id_vagon'=>['required',
                    Rule::exists('vagones', '')->where(function ($query) {
                        $query->where('activo_vagon', 'a');
                    })
                ],
                'wagons.*.estado_parada' => 'required|in:a,n'
            ],
            ['wagons.required' =>'Los vagones debe ser obligatorio',
                'wagons.*.id_vagon.required' =>'El id_vagon debe ser obligatorio',
                'wagons.*.estado_parada.required' =>'El estado_parada debe ser obligatorio',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'array' =>'El :atribute debe ser un array',
                'wagons.*.id_vagon.exists'=>'El vagon no existe o no esta activo',]
        );
    }
}