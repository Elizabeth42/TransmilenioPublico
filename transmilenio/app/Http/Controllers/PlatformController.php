<?php

namespace App\Http\Controllers;

use App\Platform;
use App\Portal;
use App\Station;
use App\Trunk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Platform::all()->toJson(), 200)->header('Content-Type', 'application/json');
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
            $trunks = $station->trunks()->get();
            // esto es para garantizar que la estacion tenga asociada una troncal)
            if ($trunks->Count() == 0)
                return response('{"error": "la estacion no puede ser asignada pues no se le ha asignado una troncal"}', 300)->header('Content-Type', 'application/json');
            //ahora es necesario validar que el id de la troncal que se ingreso si corresponda a la estacion
            $id_trunk = $request->input('id_troncal');
            if ($trunks->reject(function ($trunk) use ($id_trunk) { return $trunk->id_troncal != $id_trunk;})->count() != 1)
                return response('{"error": "la troncal establecida no corresponde a la estacion seleccionada"}', 300)->header('Content-Type', 'application/json');
            // esto es para garantizar  que no haya  ya una plataforma asociada a dicha estacion
            if (Platform::whereNotNull('ID_ESTACION')->where('ID_ESTACION','=',$station->id_estacion)->count() > 0 )
                return response('{"error": "ya hay una plataforma asociada a la estacion seleccionada y por estacion solo puede haber una plataforma"}', 300)->header('Content-Type', 'application/json');
            //esto es para garantizar que la estacion se encuentre activa
            if ($station->activo_estacion=='n')
                return response('{"error": "La estacion no se encuentra activa por tanto no se le puede asignar una plataforma"}', 300)->header('Content-Type', 'application/json');
            $created = Platform::create($validator->validated());
            return response('{ "id": ' . $created->id_portal . '}', 200)->header('Content-Type', 'application/json');

        }else{
            $portal = Portal::find($request->input('id_portal'));
            // garantiza que la estacion exista
            if (!isset($portal))
                return response('{"error": "El portal no existe"}', 300)->header('Content-Type', 'application/json');
            if ($portal->activo_portal =='n')
                return response('{"error": "El portal no se encuentra activo por tanto no se le puede asignar una plataforma"}', 300)->header('Content-Type', 'application/json');
            $created = Platform::create($validator->validated());
            return response('{ "id": ' . $created->id_portal . '}', 200)->header('Content-Type', 'application/json');
        }
    }


        /**
     * Display the specified resource.
     *
     * @param  \App\Platform  $platform
     * @return \Illuminate\Http\Response
     */
    public function show(Platform $platform)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Platform  $platform
     * @return \Illuminate\Http\Response
     */
    public function edit(Platform $platform)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Platform  $platform
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Platform $platform)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Platform  $platform
     * @return \Illuminate\Http\Response
     */
    public function destroy(Platform $platform)
    {
        //
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['numero_plataforma' => 'required|max:2',
                'activo_plataforma' => 'required|in:a,n',
                'id_troncal'=>'integer',
                'id_portal'=>'integer',
                'id_estacion'=>'integer',
            ],
            ['required' => ' El :attribute es obligatorio.',
                'max' => ' El :attribute no debe exceder los :max caracteres.',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'integer'=> 'El :attribute debe ser de tipo entero']
        );
    }
}
