<?php

namespace App\Http\Controllers;

use App\Trunk;
use Exception;
use http\Message\Body;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TrunkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->header('active')) {
            $active = request()->header('active');
            return Trunk::where('activo_troncal', '=', $active)->get();
        }
        return Trunk::all();
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
        $validator = $this->custom_validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        }
        $created = Trunk::create($validator->validated());
        return response('{ "id": ' . $created->id_troncal . '}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trunk = Trunk::find($id);
        if (!isset($trunk))
            return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');
        return response($trunk->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Trunk  $trunk
     * @return \Illuminate\Http\Response
     */
    public function edit(Trunk $trunk)
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
        $trunk = Trunk::find($id);
        if (!isset($trunk))
            return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $updated = $trunk->update($validator->validated());
        if ($trunk->wasChanged('activo_troncal')){
            $trunk->enable($request->input('activo_troncal'));
        }
        if ($updated)
            return response($trunk->toJson(), 200)->header('Content-Type', 'application/json');
        else
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     *pe
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trunk = Trunk::find($id);
        if (!isset($trunk))
            return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');

        if ($trunk->portals()->count() > 0)
            return response('{ "error": "La troncal tiene portales asociados y no puede ser eliminada"}', 300)->header('Content-Type', 'application/json');
        try {
            $deleted = $trunk->delete();
        } catch (Exception $e) {
            $deleted = false;
        }
        if ($deleted)
            return response('{ "success": "La troncal fue eliminada"}', 200)->header('Content-Type', 'application/json');
        else
            return response('{ "error": "La troncal no pudo ser eliminada"}', 300)->header('Content-Type', 'application/json');
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            [
                'nombre_troncal' => 'required|max:50',
                'letra_troncal'=> 'required|max:2|unique:App\Trunk',
                'color_troncal' => 'required|max:7',
                'activo_troncal' => 'required|in:a,n'
            ],
            ['max' => ' El :attribute no debe exceder los :max caracteres.',
             'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
             'required'=> 'El :attribute debe ser obligatorio',
             'unique'=> 'El :attribute debe ser unico'
            ]
        );
    }
}
