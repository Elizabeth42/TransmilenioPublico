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
            $portal = Portal::find($request->input('id_portal'));
            // garantiza que el portal exista
            if (!isset($portal))
                return response('{"error": "El portal no existe"}', 300)->header('Content-Type', 'application/json');
            // para validar que el portal al que se desea asignar se encuentre activo
            if ($portal->activo_portal =='n')
                return response('{"error": "El portal no se encuentra activo por tanto no se le puede asignar una plataforma"}', 300)->header('Content-Type', 'application/json');
            $created = Platform::create($validator->validated());
            return response('{ "id": ' . $created->id_portal . '}', 200)->header('Content-Type', 'application/json');

    }


        /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $platform = Platform::find($id);
        if (!isset($platform))
            return response('{"error": "La plataforma no existe"}', 300)->header('Content-Type', 'application/json');
        return response($platform->toJson(), 200)->header('Content-Type', 'application/json');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $platform = Portal::find($id);
        if (!isset($platform))
            return response('{"error": "La plataforma no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $portal = Portal::find($request->input('id_portal'));
        // garantiza que el portal exista
        if (!isset($portal))
            return response('{"error": "El portal no existe"}', 300)->header('Content-Type', 'application/json');
        // para validar que el portal al que se desea asignar se encuentre activo
        if ($portal->activo_portal =='n')
            return response('{"error": "El portal no se encuentra activo por tanto no se le puede asignar una plataforma"}', 300)->header('Content-Type', 'application/json');
        $updated = $platform->update($validator->validated());
        if ($updated)
            return response($platform->toJson(), 200)->header('Content-Type', 'application/json');
        else
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
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
                'id_portal'=>'integer',

            ],
            ['required' => ' El :attribute es obligatorio.',
                'max' => ' El :attribute no debe exceder los :max caracteres.',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'integer'=> 'El :attribute debe ser de tipo entero']
        );
    }
}
