<?php

namespace App\Http\Controllers;

use App\TimeRouteAssignment;
use App\Travel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TravelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Travel::all()->toJson(), 200)->header('Content-Type', 'application/json');
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
        //se encargara de crear el viaje con la informacion del json
        $created = Travel::create($valid[1]);
        return response($created->toJson(), 200)->header('Content-Type', 'application/json');
    }

    private function validateModel($model){
        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($model);
        if ($validator->fails()) {
            return [false, $validator->errors()->toJson()];
        }
        $exist =Travel::where('id_asignacion_ruta','=',$model['id_asignacion_ruta'])->where('fecha_inicio_viaje','=',$model['fecha_inicio_viaje'])->count();
        $asignacion = TimeRouteAssignment::find($model['id_asignacion_ruta']);
        if($exist>0)
            return [false , 'la asignacion y fecha de inicio ya fueron asignadas'];
        $validateDate = Carbon::parse($asignacion->fecha_inicio_operacion)->gt($model['fecha_inicio_viaje']);
        if($validateDate)
            return [false , 'la fecha de inicio del viaje no puede ser menor que la fecha de inicio de operacion establecida en la asignacion'];
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
        $travel = Travel::find($id);
        if (!isset($travel))
            return response('{"errors":"El viaje no existe"}', 300)->header('Content-Type', 'application/json');
        return response($travel->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Travel  $travel
     * @return \Illuminate\Http\Response
     */
    public function edit(Travel $travel)
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
        $travel = Travel::find($id);
        if (!isset($travel))
            return response('{"errors":"El viaje no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $travel->fill($validator->validated());
        $asignacion = TimeRouteAssignment::find($request->input('id_asignacion_ruta'));

        if ($travel->isDirty('id_asignacion_ruta')||$travel->isDirty('fecha_inicio_viaje')){
            $exist =Travel::where('id_asignacion_ruta','=',$request->input('id_asignacion_ruta'))->whereDate('fecha_inicio_viaje','=',$request->input('fecha_inicio_viaje'))->count();
            if($exist>0)
                return response('{"errors":"la asignacion y fecha de inicio ya fueron asignadas"}', 300)->header('Content-Type', 'application/json');
            $validateDate = Carbon::parse($asignacion->fecha_inicio_operacion)->gt($request->input('fecha_inicio_viaje'));
            if($validateDate)
                return response('{"errors":"la fecha de inicio del viaje no puede ser mayor que la fecha de inicio de operacion establecida en la asignacion"}', 300)->header('Content-Type', 'application/json');
        }

        if ($travel){
            $travel->save();
            return response($travel->toJson(), 200)->header('Content-Type', 'application/json');
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
        $travel = Travel::find($id);
        if (!isset($travel))
            return response('{"errors":"El viaje no existe"}', 300)->header('Content-Type', 'application/json');
        try {
            $deleted = $travel->delete();
        } catch (Exception $e) {
            $deleted = false;
        }
        if ($deleted)
            return response('{ "success": "El viaje fue eliminado"}', 200)->header('Content-Type', 'application/json');
        else
            return response('{ "errors":"El viaje no pudo ser eliminada"}', 300)->header('Content-Type', 'application/json');
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['fecha_inicio_viaje' => 'required|date',
                'fecha_fin_viaje' => 'date|after:fecha_inicio_viaje|nullable',
                'id_asignacion_ruta'=>['required',
                    Rule::exists('asignacion_ruta_horario', '')->where(function ($query) {
                        $query->where('activo_asignacion', 'a');
                    })
                ],
            ],
            ['date'=> 'El :attribute debe ser una fecha con hora',
                'required'=> 'El :attribute es obligatorio',
                'id_asignacion_ruta.exists'=>'La asignacion no existe o no esta activa',
                'after'=>'La fecha de fin de viaje debe ser posterior a la del inicio de viaje'
            ]
        );
    }

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(Travel::class)->make();
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
                Travel::create($model);
            else
                $errors->add($valid[1]);
        }
        return response('{"message": "Congratulations!!!!!!!!!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * por medio de este metodo genera automaticamente la cantidad de viajes que le ingrese por parametro
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
                'Content-disposition' => 'attachment; filename=Travel'.$amount.'Random.json'
            ]);
    }
}
