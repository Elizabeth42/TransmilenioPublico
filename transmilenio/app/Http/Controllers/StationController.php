<?php

namespace App\Http\Controllers;

use App\Station;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class StationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(request()->header('active')) {
            $active = request()->header('active');
            return Station::where('activo_estacion', '=', $active)->get();
        }
        return Station::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $valid = $this->validateModel($request->all());
        if(!$valid[0])
            return response('{"errors":'.( strrpos($valid[1], '}') ? $valid[1] :'"'.$valid[1].'"').'}', 400)->header('Content-Type', 'application/json');
        //se encargara de crear el portal con la informacion del json
        $created = Station::create($valid[1]);
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $station = Station::find($id);
        if (!isset($station))
            return response('{"errors":"La estacion no existe"}', 400)->header('Content-Type', 'application/json');
        return response($station->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Station $station
     * @return \Illuminate\Http\Response
     */
    public function edit(Station $station)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        $station = Station::find($id);
        if (!isset($station))
            return response('{"errors":"La estacion no existe"}', 400)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails()) {
            return response('{"errors": '. $validator->errors()->toJson().'}',  400)->header('Content-Type', 'application/json');
        }
        $updated = $station->update($validator->validated());
        if ($station->wasChanged('activo_estacion')){
            $station->enable($request->input('activo_estacion'));
        }
        if ($updated)
            return response($station->toJson(), 200)->header('Content-Type', 'application/json');
        else
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $station = Station::find($id);
        if (!isset($station))
            return response('{"errors":"La estacion no existe"}', 400)->header('Content-Type', 'application/json');
        $state = $station->activo_estacion == 'a' ? 'n' : 'a';
        if ($station){
            $station->enable($state);
            $station->save();
            return response($station->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['nombre_estacion' => 'required|max:50',
                'activo_estacion' => 'required|in:a,n'
            ],
            ['max' => ' El :attribute no debe exceder los :max caracteres.',
                'required'=> 'El :attribute debe ser obligatorio',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo']
        );
    }

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(Station::class)->make();
//            $validator = $this->custom_validator($model->attributesToArray());
//            if (!$validator->fails())
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
                Station::create($model);
            else
                $errors->add($valid[1]);
        }
        return response('{"message": "¡Estaciones cargadas satisfactoriamente!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

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
        return response( '{"message": "Estaciones generadas satisfactoriamente", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');;
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
                'Content-disposition' => 'attachment; filename=Station'.$amount.'Random.json'
            ]);
    }
}
