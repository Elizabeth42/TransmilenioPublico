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
        $valid = $this->validateModel($request->all());
        if(!$valid[0])
            return response('{"error": "'.$valid[1].'"}', 300)->header('Content-Type', 'application/json');
        //se encargara de crear el portal con la informacion del json
        $created = BusType::create($valid[1]);
        return response($created->toJson(), 200)->header('Content-Type', 'application/json');
    }

    private function validateModel($model){
        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($model);
        if ($validator->fails()) {
            return [false, $validator->errors()->toJson()];
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

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(BusType::class)->make();
            $validator = $this->custom_validator($model->attributesToArray());
            if (!$validator->fails())
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
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $document = $request->file('file');
        $json =  \GuzzleHttp\json_decode(file_get_contents($document->getRealPath()));
        $errors = collect();
        foreach ($json as $item) {
            $model = get_object_vars($item);
            $valid = $this->validateModel($model);
            if ($valid[0])
                BusType::create($model);
            else
                $errors->add(['error' => $valid[1]]);
        }
        return response('{"message": "Congratulations Prosseced BusTypes!!!!!!!!!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * por medio de este metodo genera automaticamente la cantidad de tipos de buses que le ingrese por parametro
     * @param $amount
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function saveRandom($amount) {
        $result = $this->getRandom($amount);
        foreach ($result as $model) {
            $model->save();
        }
        return response( '{"message": "Reaady"}', 200)->header('Content-Type', 'application/json');;
    }
}
