<?php

namespace App\Http\Controllers;

use App\Trunk;
use Exception;
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
        return response(Trunk::all()->toJson(), 200)->header('Content-Type', 'application/json');
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
        if ($updated)
            return response($trunk->toJson(), 200)->header('Content-Type', 'application/json');
        else
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trunk = Trunk::find($id);
        if (!isset($trunk))
            return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');
        if ($trunk->portals()->count() > 0)
            return response('{ "error": "La troncal tiene portales asociadas y no puede ser eliminada"}', 300)->header('Content-Type', 'application/json');
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
                'letra_troncal'=> 'required|max:2',
                'color_troncal' => 'required|max:7'
            ],
            ['max' => ' El :attribute no debe exceder los :max caracteres.']
        );
    }
}
