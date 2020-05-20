<?php

namespace App\Http\Controllers;

use App\Platform;
use App\Portal;
use App\Station;
use App\Trunk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlatformController extends Controller
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
            return Platform::where('activo_plataforma', '=', $active)->get();
        }
        return Platform::all();
     //   return response(Platform::all()->toJson(), 200)->header('Content-Type', 'application/json');
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
        $created = Platform::create($validator->validated());
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
        $platform = Platform::find($id);
        if (!isset($platform))
            return response('{"error": "La plataforma no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');

        $updated = $platform->update($validator->validated());
        //esto es para establecer si los hijos se activan o inactivan
        if ($platform->wasChanged('activo_plataforma')){
            $platform->enable($request->input('activo_plataforma'));
        }
        if ($updated)
            return response($platform->toJson(), 200)->header('Content-Type', 'application/json');
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
        $platform = Platform::find($id);
        if (!isset($platform))
            return response('{"error": "La plataforma no existe"}', 300)->header('Content-Type', 'application/json');
        $portal = Portal::find($platform->id_portal);
        if ($portal->activo_portal == 'n')
            return response('{"error": "El portal se encuentra inactivo"}', 300)->header('Content-Type', 'application/json');
        $state = $platform->activo_plataforma == 'a' ? 'n' : 'a';
        if ($platform){
            $platform->enable($state);
            $platform->save();
            return response($platform->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['numero_plataforma' => 'required|max:2',
                'activo_plataforma' => 'required|in:a,n',
                //con esto valido que la troncal sea obligatoria, que la troncal exista y que se encuentre activa
                'id_portal'=>['required','integer',
                    Rule::exists('portales', '')->where(function ($query) {
                        $query->where('activo_portal', 'a');
                    })
                ]
            ],
            ['required' => ' El :attribute es obligatorio.',
                'max' => ' El :attribute no debe exceder los :max caracteres.',
                'id_portal.exists'=>'El portal no existe o no esta activa',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'integer'=> 'El :attribute debe ser de tipo entero']
        );
    }

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(Platform::class)->make();
            $valid = \PlatformSeed::validate($model);
            if ($valid)
                $result->add($model);
        }
        return $result;
    }
}
