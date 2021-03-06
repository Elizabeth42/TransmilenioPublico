<?php

namespace App\Http\Controllers;

use App\Route;
use App\Wagon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use RouteSeed;

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
            return response('{"errors":"La ruta no existe"}', 400)->header('Content-Type', 'application/json');
        if (request()->header('active')) {
            $active = request()->header('active');
            return response($route->wagons()->withPivot('estado_parada', 'orden')->with('trunk_station')->with('platform')->where('paradas.estado_parada', '=', $active)->get());
        }
        $list = $route->wagons()->withPivot('estado_parada', 'orden')->with('platform')->with('trunk_station')->orderBy('pivot_orden','asc')->get();
        return response($list, 200)->header('Content-Type', 'application/json');
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
        $valid = $this->validateModel($request->all(), $id);
        if(!$valid[0])
            return response('{"errors":'.( strrpos($valid[1], '}') ? $valid[1] :'"'.$valid[1].'"').'}', 400)->header('Content-Type', 'application/json');
        $route = Route::find($id);
        foreach ($valid[1] as $parada) {
            $id_wagon = $parada['id_vagon'];
            $parada['id_vagon']=$id_wagon;
            $parada['orden'] = $this->assign_order($route);
            if(!$valid[2]->has($id_wagon))
                $route->wagons()->attach($id_wagon,['estado_parada' => $parada['estado_parada'], 'orden' => $parada['orden']]);
        }
        return response('{"success": "Agregadas correctamente los vagones a la ruta", "errors": '.$valid[2]->toJson().'}', 200)->header('Content-Type', 'application/json');
    }

    private function assign_order($route)
    {
        // El ultimo vagon asignado a esa ruta
        $last_bus_stop = $route->wagons()->withPivot('orden')->orderBy('orden', 'DESC')->first();
        // basicamente se pregunta que si existe un ultomo vagon y a partir de ello se asigna el orden
        return isset($last_bus_stop) ? $last_bus_stop->pivot->orden + 1 : 1;
    }
    /*
     * select * from (
            select p.orden from paradas p
            join vagones v on (p.id_vagon=v.id_vagon)
            where v.id_vagon= 13
            order by p.orden desc)
        where rownum=1;
     * */

    private function validateModel($model, $id){
        $route = Route::find($id);
        if (!isset($route))
            return [false, 'La ruta '.$id.' no existe'];
        if ($route->activo_ruta == 'n')
            return [false, 'La ruta '.$id.' se encuentra inactiva'];
        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($model);
        if ($validator->fails()) {
            return [false, $validator->errors()->toJson()];
        }
        $errors = collect();
        $stops = collect();
        $paradas = $validator->validated()['wagons'];
        foreach ($paradas as $parada) {
            $id_wagon = $parada['id_vagon'];
            $wagon = Wagon::find($id_wagon);
            if(!isset($wagon)){
                $errors->add('El vagon '.$id_wagon.' no existe');
                continue;
            }
            if($wagon->activo_vagon != 'a'){
                $errors->add('El vagon '.$id_wagon.' no se encuentra activo');
                continue;
            }
            // permitira establecer si la ruta ya tiene este vagon asociado y si el vagon se encuentra activo
            if ($route->hasWagon($id_wagon)){
                $errors->add('El vagon '.$id_wagon.' ya se encuentra asignado a la ruta '.$id);
                continue;
            }
            $parada['id_vagon']=$id_wagon;
            $stops->add($parada);
        }
        return [true, $stops, $errors];
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
            return response('{"errors":"La ruta no existe"}', 400)->header('Content-Type', 'application/json');
        if ($route->activo_ruta == 'n')
            return response('{"errors":"La ruta se encuentra inactiva"}', 400)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response('{"errors": '. $validator->errors()->toJson().'}',  400)->header('Content-Type', 'application/json');
        $paradas = $validator->validated()['wagons'];
        $new_wagons = [];
        foreach ($paradas as $key => $wagon) {
            $wagonSearch = Wagon::find($wagon['id_vagon']);
            if ($wagonSearch->activo_vagon == 'n')
                return response('{"errors":"El vagon ' . $wagon['id_vagon'] . ' se encuentra inactivo"}', 400)->header('Content-Type', 'application/json');
            $new_wagon = [];
            $new_wagon['estado_parada'] = $wagon['estado_parada'];
            $new_wagon['orden'] = $key;
            $new_wagons[$wagon['id_vagon']] = $new_wagon;
        }
        $route->wagons()->sync($new_wagons);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $idR
     * @param int $idW
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete_wagon_to_route($idR, $idW)
    {
        $route = Route::find($idR);
        if (!isset($route))
            return response('{"errors":"La ruta no existe"}',
                400)->header('Content-Type', 'application/json');
        if ($route->activo_ruta == 'n')
            return response('{"errors":"La ruta se encuentra inactiva"}',
                400)->header('Content-Type', 'application/json');
        if ($route->hasWagon($idW)) {
            $wagon_r = $route->wagons()->withPivot('estado_parada')->where('vagones.id_vagon', '=', $idW)->first();
            if ($wagon_r->activo_vagon == 'n')
                return response('{"errors":"El vagon se encuentra inactivo"}',
                    400)->header('Content-Type', 'application/json');
            $new_state = $wagon_r->pivot->estado_parada == 'a' ? 'n' : 'a';
            $route->wagons()->syncWithoutDetaching([$wagon_r->id_vagon => ['estado_parada' => $new_state]]);
            return response($route->wagons()->withPivot('estado_parada')
                ->where('vagones.id_vagon', '=', $idW)->first()->toJson(),
                200)->header('Content-Type', 'application/json');
        } else {
            return response('{"errors":"El vagon no se encuentra asociado a esta ruta"}',
                400)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            [
                'wagons' => 'required|array',
                'wagons.*.id_vagon' => 'required',
                'wagons.*.estado_parada' => 'required|in:a,n'
            ],
            ['wagons.required' => 'Los vagones debe ser obligatorio',
                'wagons.*.id_vagon.required' => 'El id_vagon debe ser obligatorio',
                'wagons.*.estado_parada.required' => 'El estado_parada debe ser obligatorio',
                'in' => 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'array' => 'El :atribute debe ser un array',
                'wagons.*.id_vagon.exists' => 'El vagon no existe o no esta activo',]
        );
    }

    public function getRandom($amount)
    {
        $result = collect();
        $wagons = \App\Wagon::all();
        $routes = \App\Route::all();
        for ($i = 0; $i < $amount; $i++) {
            $randomW = $wagons->random();
            $randomR = $routes->random();
            $stop = \RouteSeed::addStop($randomW, $randomR);
            if (isset($stop))
                $result->add($stop);
        }
        return $result;
    }

    public function fillFromJson(Request $request){
        $validator = Validator::make($request->all(),
            [
                'file' => 'required|file|mimetypes:application/json|max:20000',
            ]
        );
        if ($validator->fails())
            return response('{"errors": '. $validator->errors()->toJson().'}',  400)->header('Content-Type', 'application/json');
        $document = $request->file('file');
        $errors = collect();
        $json =  \GuzzleHttp\json_decode(file_get_contents($document->getRealPath()), true);
        foreach ($json as $stop)
        {
            $route = Route::find($stop['id_ruta']);
            $valid = $this->validateModel(['wagons'=>[$stop]], $stop['id_ruta']);
            if(!$valid[0])
                $errors->add($valid[1]);
            else {
                if (sizeof($valid[2]) == 1)
                    $errors->add($valid[2][0]);
                foreach ($valid[1] as $model) {
                    $model['orden'] = $this->assign_order($route);
                    $route->wagons()->attach($model['id_vagon'], ['estado_parada' => $model['estado_parada'], 'orden'=>$model['orden']]);
                }
            }
        }
        return response('{"message": "¡Paradas cargadas satisfactoriamente!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * por medio de este metodo genera automaticamente la cantidad de buses que le ingrese por parametro
     * @param $amount
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function saveRandom($amount) {
        $wagons = \App\Wagon::all();
        $routes = \App\Route::all();
        $errors = collect();
        for ($i = 0; $i < $amount; $i++) {
            $randomW = $wagons->random();
            $randomR = $routes->random();
            $stop = \RouteSeed::addStop($randomW, $randomR);
            $valid = $this->validateModel(['wagons'=>[$stop]], $randomR->getKey());
            if(!$valid[0])
                $errors->add($valid[1]);
            else {
                if (sizeof($valid[2]) == 1)
                    $errors->add($valid[2][0]);
                foreach ($valid[1] as $model) {
                    $model['orden'] = $this->assign_order($randomR);
                    $randomR->wagons()->attach($model['id_vagon'], ['estado_parada' => $model['estado_parada'], 'orden'=>$model['orden']]);
                }
            }
        }
        return response( '{"message": "¡Paradas generadas satisfactoriamente!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');;
    }


    /**
     * Este metodo permite guardar el archivo json de una cantidad de elementos random creados segun el parametro que entra
     * @param $amount
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function saveFactoryJson($amount){
        $content = $this->getRandom($amount);
        return response($content)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Content-disposition' => 'attachment; filename=Stop'.$amount.'Random.json'
            ]);
    }

}
