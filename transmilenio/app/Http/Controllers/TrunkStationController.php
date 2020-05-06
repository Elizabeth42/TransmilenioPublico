<?php

namespace App\Http\Controllers;

use App\Station;
use App\Trunk;
use App\TrunkStation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TrunkStationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(TrunkStation::all()->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        }
        //si estacion no es nulo significa que se asigna a una estacion y toca realizar las validaciones necesarias
        if ($request->input('id_estacion') != 0) {
            $station = Station::find($request->input('id_estacion'));
            // garantiza que la estacion exista
            if (!isset($station))
                return response('{"error": "La estacion no existe"}', 300)->header('Content-Type', 'application/json');
            $troncal = Trunk::find($request->input('id_troncal'));
            // garantiza que la troncal exista
            if (!isset($troncal))
                return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');
            //esto es para garantizar que la estacion se encuentre activa
            if ($station->activo_estacion=='n')
                return response('{"error": "La estacion no se encuentra activa por tanto no puede ser asignada a una troncal_estacion"}', 300)->header('Content-Type', 'application/json');
            //esto es para garantizar que la troncal se encuentra activa
            if ($troncal->activo_troncal=='n')
                return response('{"error": "La troncal no se encuentra activa por tanto no puede ser asignada a una troncal_estacion"}', 300)->header('Content-Type', 'application/json');
            //finalmente se requiere garantizar que esa troncal no tenga asignada ya esa estacion pues ambas deben ser unicas
            if ($station->hasTrunk($troncal->id_troncal))
                return response('{"error": "la estacion ya tiene esa troncal asociada"}', 300)->header('Content-Type', 'application/json');

            // se procede con la creacion de la troncal estacion
            $created = TrunkStation::create($validator->validated());
            return response('{ "id": ' . $created->id_troncal_estacion . '}', 200)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $troncalStacion = TrunkStation::find($id);
        if (!isset($troncalStacion))
            return response('{"error": "La troncal_estacion no existe"}', 300)->header('Content-Type', 'application/json');
        return response($troncalStacion->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TrunkStation  $trunkStation
     * @return \Illuminate\Http\Response
     */
    public function edit(TrunkStation $trunkStation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $troncalStacion = TrunkStation::find($id);
        if (!isset($troncalStacion))
            return response('{"error": "La troncal_estacion no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $station = Station::find($request->input('id_estacion'));
        // garantiza que la estacion exista
        if (!isset($station))
            return response('{"error": "La estacion no existe"}', 300)->header('Content-Type', 'application/json');
        $troncal = Trunk::find($request->input('id_troncal'));
        // garantiza que la troncal exista
        if (!isset($troncal))
            return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');
        // garantiza que la nueva estacion este activa
        if ($station->activo_estacion=='n')
            return response('{"error": "La estacion no se encuentra activa por tanto no puede ser asignada a una troncal_estacion"}', 300)->header('Content-Type', 'application/json');
        //esto es para garantizar que la  nueva troncal se encuentra activa
        if ($troncal->activo_troncal=='n')
            return response('{"error": "La troncal no se encuentra activa por tanto no puede ser asignada a una troncal_estacion"}', 300)->header('Content-Type', 'application/json');
        //finalmente se requiere garantizar que esa troncal no tenga asignada ya esa estacion pues ambas deben ser unicas
        if ($station->hasTrunk($troncal->id_troncal))
            return response('{"error": "la estacion ya tiene esa troncal asociada"}', 300)->header('Content-Type', 'application/json');
        // con esto ya validado se procede a la actualizacion
        $updated = $troncalStacion->update($validator->validated());
        if ($updated)
            return response($troncalStacion->toJson(), 200)->header('Content-Type', 'application/json');
        else
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TrunkStation  $trunkStation
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrunkStation $trunkStation)
    {
        //
    }


    private function custom_validator($data)
    {
        return Validator::make($data,
            ['activo_troncal_estacion' => 'required|in:a,n',
                'id_troncal'=>'required|integer',
                'id_estacion'=>'required|integer',
            ],
            ['required' => ' El :attribute es obligatorio.',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'integer'=> 'El :attribute debe ser de tipo entero']
        );
    }
}
