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
        $valid = $this->validateModel($request->all());
        if(!$valid[0])
            return response('{"errors":"'.$valid[1].'"}', 300)->header('Content-Type', 'application/json');

        //se encargara de crear el portal con la informacion del json
        $created = Trunk::create($valid[1]);
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
        $trunk = Trunk::find($id);
        if (!isset($trunk))
            return response('{"errors":"La troncal no existe"}', 300)->header('Content-Type', 'application/json');
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
            return response('{"errors":"La troncal no existe"}', 300)->header('Content-Type', 'application/json');
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
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
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
            return response('{"errors":"La troncal no existe"}', 300)->header('Content-Type', 'application/json');
        $state = $trunk->activo_troncal == 'a' ? 'n' : 'a';
        if ($trunk){
            $trunk->enable($state);
            $trunk->save();
            return response($trunk->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }


    private function custom_validator($data)
    {
        return Validator::make($data,
            [
                'nombre_troncal' => 'required|max:50',
                'letra_troncal'=> 'required|max:2',
                'color_troncal' => 'required|max:7',
                'activo_troncal' => 'required|in:a,n'
            ],
            ['max' => ' El :attribute no debe exceder los :max caracteres.',
             'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
             'required'=> 'El :attribute debe ser obligatorio'
            ]
        );
    }

    /**
     * Permitira generar la cantidad de troncales que le entren por parametro y que cumplan con las validaciones
     * (visualmente no en la base de datos)
     * @param $amount
     * @return \Illuminate\Support\Collection
     */
    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(Trunk::class)->make();
//            $validator = $this->custom_validator($model->attributesToArray());
//            if (!$validator->fails())
                $result->add($model);
        }
        return $result;
    }

    /**
     * El request que ingresa es un documento JSON de maximo 20 Kb  por medio del validator se encarga de asegurar que
     * no haya errores, y en caso de haberlos esos datos no sera cargados continuando con los siguientes
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
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
                Trunk::create($model);
            else
                $errors->add($valid[1]);
        }
        return response('{"message": "Congratulations Prosseced BusTypes!!!!!!!!!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * por medio de este metodo genera automaticamente la cantidad de troncales que le ingrese por parametro
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

    public function saveFactoryJson($amount){
        $content = $this->getRandom($amount);
        return response($content)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Content-disposition' => 'attachment; filename=Trunk'.$amount.'Random.json'
            ]);
    }

//    /**
//     * Permitira
//     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
//     */
//    public function download(){
//        $content = \App\Trunk::all();
//        return response($content)
//            ->withHeaders([
//                'Content-Type' => 'application/json',
//                'Content-disposition' => 'attachment; filename=users.json'
//            ]);
//    }
}
