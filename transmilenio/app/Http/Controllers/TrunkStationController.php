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
            return TrunkStation::with('trunk')->with('station')->
            where('activo_troncal_estacion', '=', $active)->get();
        }
        return TrunkStation::with('trunk')->with('station')->get();
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
        $valid = $this->validateModel($request->all());
        if(!$valid[0])
            return response('{"errors":'.( strrpos($valid[1], '}') ? $valid[1] :'"'.$valid[1].'"').'}', 400)->header('Content-Type', 'application/json');

        // se procede con la creacion de la troncal estacion
        $created = TrunkStation::create($valid[1]);
        return response($created->toJson(), 200)->header('Content-Type', 'application/json');
     //   }
    }

    private function validateModel($model){
        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($model);
        if ($validator->fails()) {
            return [false, $validator->errors()->toJson()];
        }
        // permitira validar si en el modelo viene la id_troncal y a id_estacion
        $troncal = key_exists('id_troncal', $model) ? Trunk::find($model['id_troncal']): null;
        $station = key_exists('id_estacion', $model) ? Station::find($model['id_estacion']): null;

        // permitira validar que almenos una de las foreign key venga activa
        if ($troncal==null || $station ==null){
            return [false , 'La troncal estacion necesita de un id de troncal y un id de estacion'];
        }
        if(!isset($troncal)|| !isset($station)){
            return [false , 'La troncal o la estacion no existe'];
        }
        if($troncal->activo_troncal == 'n' || $station->activo_estacion == 'n'){
            return [false , 'La troncal o la estacion se encuentra inactiva'];
        }
        //finalmente se requiere garantizar que esa troncal no tenga asignada ya esa estacion pues ambas deben ser unicas
        if ($station->hasTrunk($troncal->id_troncal))
            return [false , 'la estacion ya tiene esa troncal asociada'];

        return [true, $validator->validated()];
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
            return response('{"errors":"La troncal_estacion no existe"}', 400)->header('Content-Type', 'application/json');
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
            return response('{"errors":"La troncal_estacion no existe"}', 400)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response('{"errors": '. $validator->errors()->toJson().'}',  400)->header('Content-Type', 'application/json');
        $troncalStacion->fill($validator->validated());
        $station = Station::find($request->input('id_estacion'));
        $troncal = Trunk::find($request->input('id_troncal'));

        if(!isset($troncal)|| !isset($station)){
            return response('{"errors":"La troncal o la estacion no existe"}', 400)->header('Content-Type', 'application/json');
        }
        //es necesario saber si se esta modificando de una troncal
        if($troncalStacion->isDirty('id_troncal')){
            if($troncal->activo_troncal == 'n'){
                return response('{"errors":"La troncal no se encuentra activa"}', 400)->header('Content-Type', 'application/json');
            }
            //se requiere garantizar que esa troncal no tenga asignada ya esa estacion pues ambas deben ser unicas
            if ($station->hasTrunk($troncal->id_troncal))
                return response('{"errors":"la estacion ya tiene esa troncal asociada"}', 400)->header('Content-Type', 'application/json');
        }
        //es necesario saber si se esta modificando una estacion
        if($troncalStacion->isDirty('id_estacion')){
            if($station->activo_estacion == 'n'){
                return response('{"errors":"La estacion no se encuentra activa"}', 400)->header('Content-Type', 'application/json');
            }
            //se requiere garantizar que esa troncal no tenga asignada ya esa estacion pues ambas deben ser unicas
            if ($station->hasTrunk($troncal->id_troncal))
                return response('{"errors":"la estacion ya tiene esa troncal asociada"}', 400)->header('Content-Type', 'application/json');
        }
        //esto se hace para establecer si los hijos se activan o inactivan
        if($troncalStacion->isDirty('activo_troncal_estacion')){
            $troncalStacion->enable($request->input('activo_troncal_estacion'));
        }

        if ($troncalStacion){
            $troncalStacion->save();
            return response($troncalStacion->toJson(), 200)->header('Content-Type', 'application/json');
        }
        else{
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
        }
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
            return response('{"errors":"La troncal_estacion no existe"}', 400)->header('Content-Type', 'application/json');
        $troncal = Trunk::find($troncalStacion->id_troncal);
        $estacion = Station::find($troncalStacion->id_estacion);
        if($troncal->activo_troncal == 'n' || $estacion->activo_estacion == 'n'){
            return response('{"errors":"La troncal o la estacion no se encuentra activa"}', 400)->header('Content-Type', 'application/json');
        }
        $state = $troncalStacion->activo_troncal_estacion == 'a' ? 'n' : 'a';
        if ($troncalStacion){
            $troncalStacion->enable($state);
            $troncalStacion->save();
            return response($troncalStacion->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }


    private function custom_validator($data)
    {
        return Validator::make($data,
            ['activo_troncal_estacion' => 'required|in:a,n',
                //con esto valido que la troncal sea obligatoria, que la troncal exista y que se encuentre activa
                'id_troncal'=>['required'
//                    , Rule::exists('troncales', '')->where(function ($query) {
//                        $query->where('activo_troncal', 'a');
//                    })
                ],
                //con esto valido que la estacion sea obligatoria, que la estacion exista y que se encuentre activa
                'id_estacion'=>['required'
//                    , Rule::exists('estaciones')->where(function ($query) {
//                        $query->where('activo_estacion', 'a');
//                    })
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
//            $valid = \TrunkStationSeed::validate($model);
//            if ($valid)
                $result->add($model);
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
        $json =  \GuzzleHttp\json_decode(file_get_contents($document->getRealPath()));
        $errors = collect();
        foreach ($json as $item) {
            $model = get_object_vars($item);
            $valid = $this->validateModel($model);
            if ($valid[0])
                TrunkStation::create($model);
            else
                $errors->add($valid[1]);
        }
        return response('{"message": "Congratulations!!!!!!!!!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * por medio de este metodo genera automaticamente la cantidad de troncal_Estacion que le ingrese por parametro
     * @param $amount
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function saveRandom($amount) {
        $result = $this->getRandom($amount);
        $errors = collect();
        foreach ($result as $model) {
            $valid = $this->validateModel($model->toArray());
            if ($valid[0])
                $model->save();
            else
                $errors->add($valid[1]);
        }
        return response( '{"message": "Reaady", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');;
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
                'Content-disposition' => 'attachment; filename=TrunkStation'.$amount.'Random.json'
            ]);
    }
}
