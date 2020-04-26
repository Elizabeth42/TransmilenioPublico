<?php

namespace App\Http\Controllers;

use App\Station;
use App\Trunk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StationTrunkController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function get_stations_from_trunk($id)
    {
        $trunk = Trunk::find($id);
        if (!isset($trunk))
            return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');
        return response($trunk->stations()->get()->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function add_stations_to_trunk(Request $request, $id)
    {
        $trunk = Trunk::find($id);
        if (!isset($trunk))
            return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $ids_to_verify = $validator->validated()['stations'];
        foreach ($ids_to_verify as $key => $id_sta) {
            if ($trunk->hasStation($id_sta))
                unset($ids_to_verify[$key]);
        }
        if (count($ids_to_verify) > 0)
            $trunk->stations()->attach($ids_to_verify);
        return response('{"success": "Agregadas correctamente las estaciones a la troncal"}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete_station_to_trunk(Request $request, $id)
    {
        $trunk = Trunk::find($id);
        if (!isset($trunk))
            return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $ids_to_verify = $validator->validated()['stations'];
        foreach ($ids_to_verify as $key => $id_sta) {
            if (!$trunk->hasStation($id_sta))
                unset($ids_to_verify[$key]);
        }
        if (count($ids_to_verify) > 0)
            $trunk->stations()->detach($ids_to_verify);
        return response('{"success": "Elminadas correctamente las estaciones de la troncal"}', 200)->header('Content-Type', 'application/json');

    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            [
                'stations' => 'required|array',
                'stations.*' => 'integer',
            ],
            ['integer' => 'Debe ser un arreglo de enteros']
        );
    }
}
