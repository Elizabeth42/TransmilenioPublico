<?php

namespace App\Http\Controllers;

use App\Bus;
use App\BusType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;

class BusController extends Controller
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
            return Bus::with('busType')->where('activo_bus', '=', $active)->get();
        }
        return Bus::with('busType')->get();
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
        //se encargara de crear el vagon con la informacion del json
        $created = Bus::create($valid[1]);
        return response($created->toJson(), 200)->header('Content-Type', 'application/json');
    }

    private function validateModel($model){
        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($model);
        if ($validator->fails()) {
            return [false, $validator->errors()->toJson()];
        }
        // permitira validar que la placa ingresada sea unica
        if(Bus::where('placabus','=',$model['placabus'])->count()>0) {
            return [false, 'La placa del bus debe ser unica'];
        }
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
        $bus = Bus::find($id);
        if (!isset($bus))
            return response('{"errors":"El bus no existe"}', 400)->header('Content-Type', 'application/json');
        return response($bus->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bus  $bus
     * @return \Illuminate\Http\Response
     */
    public function edit(Bus $bus)
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
        $bus = Bus::find($id);
        if (!isset($bus))
            return response('{"errors":"El bus no existe"}', 400)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response('{"errors": '. $validator->errors()->toJson().'}',  400)->header('Content-Type', 'application/json');
        //peermitira asignar nuevos valores al bus seleccionado
        $bus->fill($validator->validated());
        // si fue cambiada la placa del bus valide si la misma ya se encuentra registrada
        if ($bus->isDirty('placabus')){ // el isDirty permite analizar si el atributo cambio desde la ultima carga del modelo
            if($bus->where('placabus','=',$request->input('placabus'))->count()>0){
                return response('{"errors":"La placa del bus debe ser unica"}', 400)->header('Content-Type', 'application/json');
            }
        }
        //esto es para establecer si los hijos se activan o inactivan
        if ($bus->isDirty('activo_bus')){
            $bus->enable($request->input('activo_bus'));
        }
        if ($bus){
            $bus->save();
            return response($bus->toJson(), 200)->header('Content-Type', 'application/json');
        } else{
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
        $bus = Bus::find($id);
        if (!isset($bus))
            return response('{"errors":"El bus no existe"}', 400)->header('Content-Type', 'application/json');
        $type = BusType::find($bus->id_tipo_bus);
        if ($type->activo_tipo_bus == 'n')
            return response('{"errors":"El tipo bus se encuentra inactivo"}', 400)->header('Content-Type', 'application/json');
        $state = $bus->activo_bus == 'a' ? 'n' : 'a';
        if ($bus){
            $bus->enable($state);
            $bus->save();
            return response($bus->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['placabus' => 'required|max:50',
                'activo_bus' => 'required|in:a,n',
                'id_tipo_bus'=>['required',
                    Rule::exists('tipo_bus', '')->where(function ($query) {
                        $query->where('activo_tipo_bus', 'a');
                    })
                ],
            ],
            ['max' => ' La :attribute no debe exceder los :max caracteres.',
                'required'=> 'El :attribute es obligatorio',
                'id_tipo_bus.exists'=>'El tipo bus :input no existe o no esta activo',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo']
        );
    }

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(Bus::class)->make();
//            $valid = \BusSeed::validate($model);
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
                Bus::create($model);
            else
                $errors->add($valid[1]);
        }
        return response('{"message": "¡Buses cargados satisfactoriamente!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * por medio de este metodo genera automaticamente la cantidad de buses que le ingrese por parametro
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
        return response( '{"message": "¡Buses generados satisfactoriamente!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');;
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
                'Content-disposition' => 'attachment; filename=Bus'.$amount.'Random.json'
            ]);
    }
}
