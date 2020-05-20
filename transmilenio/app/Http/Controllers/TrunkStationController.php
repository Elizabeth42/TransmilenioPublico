<?php

namespace App\Http\Controllers;

use App\Station;
use App\Trunk;
use App\TrunkStation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrunkStationController extends Controller
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
            return TrunkStation::where('activo_troncal_estacion', '=', $active)->get();
        }
        return TrunkStation::all();
        //return response(TrunkStation::all()->toJson(), 200)->header('Content-Type', 'application/json');
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
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $station = Station::find($request->input('id_estacion'));
        $troncal = Trunk::find($request->input('id_troncal'));

        //finalmente se requiere garantizar que esa troncal no tenga asignada ya esa estacion pues ambas deben ser unicas
        if ($station->hasTrunk($troncal->id_troncal))
            return response('{"error": "la estacion ya tiene esa troncal asociada"}', 300)->header('Content-Type', 'application/json');

            // se procede con la creacion de la troncal estacion
            $created = TrunkStation::create($validator->validated());
            return response($created->toJson(), 200)->header('Content-Type', 'application/json');
     //   }
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
        $troncal = Trunk::find($request->input('id_troncal'));

        //se requiere garantizar que esa troncal no tenga asignada ya esa estacion pues ambas deben ser unicas
        if ($station->hasTrunk($troncal->id_troncal))
            return response('{"error": "la estacion ya tiene esa troncal asociada"}', 300)->header('Content-Type', 'application/json');

        // con esto ya validado se procede a la actualizacion
        $updated = $troncalStacion->update($validator->validated());
        //esto es para establecer si los hijos se activan o inactivan
        if ($troncalStacion->wasChanged('activo_troncal_estacion')){
            $troncalStacion->enable($request->input('activo_troncal_estacion'));
        }
        if ($updated)
            return response($troncalStacion->toJson(), 200)->header('Content-Type', 'application/json');
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
        $troncalStacion = TrunkStation::find($id);
        if (!isset($troncalStacion))
            return response('{"error": "La troncal_estacion no existe"}', 300)->header('Content-Type', 'application/json');
        $troncal = Trunk::find($troncalStacion->id_troncal);
        $estacion = Trunk::find($troncalStacion->id_estacion);
        if($troncal->activo_troncal == 'n' || $estacion->activo_estacion == 'n'){
            return response('{"error": "La troncal o la estacion no se encuentra activa"}', 300)->header('Content-Type', 'application/json');
        }
        $state = $troncalStacion->activo_troncal_estacion == 'a' ? 'n' : 'a';
        if ($troncalStacion){
            $troncalStacion->enable($state);
            $troncalStacion->save();
            return response($troncalStacion->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }


    private function custom_validator($data)
    {
        return Validator::make($data,
            ['activo_troncal_estacion' => 'required|in:a,n',
                //con esto valido que la troncal sea obligatoria, que la troncal exista y que se encuentre activa
                'id_troncal'=>['required',
                    Rule::exists('troncales', '')->where(function ($query) {
                        $query->where('activo_troncal', 'a');
                    })
                ],
                //con esto valido que la estacion sea obligatoria, que la estacion exista y que se encuentre activa
                'id_estacion'=>['required',
                    Rule::exists('estaciones')->where(function ($query) {
                        $query->where('activo_estacion', 'a');
                    })
                ],
            ],
            [
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'required'=> 'El :attribute es obligatorio',
                'id_troncal.exists'=>'La troncal no existe o no esta activa',
                'id_estacion.exists'=>'La estacion no existe o esta inactiva',
            ]
        );
    }

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(TrunkStation::class)->make();
            $valid = \TrunkStationSeed::validate($model);
            if ($valid)
                $result->add($model);
        }
        return $result;
    }
}
