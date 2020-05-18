<?php

namespace App\Http\Controllers;

use App\BusType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BusTypeController extends Controller
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
            return BusType::where('activo_tipo_bus', '=', $active)->get();
        }
        return BusType::all();
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
        $created = BusType::create($validator->validated());
        return response($created->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipoBus = BusType::find($id);
        if (!isset($tipoBus))
            return response('{"error": "El tipo de bus no existe"}', 300)->header('Content-Type', 'application/json');
        return response($tipoBus->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BusType  $busType
     * @return \Illuminate\Http\Response
     */
    public function edit(BusType $busType)
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
        $tipoBus = BusType::find($id);
        if (!isset($tipoBus))
            return response('{"error": "El tipo de bus no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $updated = $tipoBus->update($validator->validated());
        if ($tipoBus->wasChanged('activo_tipo_bus')){
            $tipoBus->enable($request->input('activo_tipo_bus'));
        }
        if ($updated)
            return response($tipoBus->toJson(), 200)->header('Content-Type', 'application/json');
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
        $tipoBus = BusType::find($id);
        if (!isset($tipoBus))
            return response('{"error": "El tipo de bus no existe"}', 300)->header('Content-Type', 'application/json');
        $state = $tipoBus->activo_tipo_bus == 'a' ? 'n' : 'a';
        if ($tipoBus){
            $tipoBus->enable($state);
            $tipoBus->save();
            return response($tipoBus->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            [
                'nombre_tipo' => 'required|max:50',
                'color' => 'required|max:7',
                'activo_tipo_bus' => 'required|in:a,n'
            ],
            ['max' => ' El :attribute no debe exceder los :max caracteres.',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'required'=> 'El :attribute debe ser obligatorio'
            ]
        );
    }
}
