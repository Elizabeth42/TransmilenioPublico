<?php

namespace App\Http\Controllers;

use App\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
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
            return Schedule::where('activo_horario', '=', $active)->get();
        }
        return Schedule::all();
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
        //se encargara de crear el horario con la informacion del json
        $created = Schedule::create($valid[1]);
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
        $schedule = Schedule::find($id);
        if (!isset($schedule))
            return response('{"error": "El horario no existe"}', 300)->header('Content-Type', 'application/json');
        return response($schedule->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
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
        $horario = Schedule::find($id);
        if (!isset($horario))
            return response('{"error": "El horario no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $updated = $horario->update($validator->validated());
        if ($horario->wasChanged('activo_horario')){
            $horario->enable($request->input('activo_horario'));
        }
        if ($updated)
            return response($horario->toJson(), 200)->header('Content-Type', 'application/json');
        else
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $horario = Schedule::find($id);
        if (!isset($horario))
            return response('{"error": "El horario no existe"}', 300)->header('Content-Type', 'application/json');
        $state = $horario->activo_horario == 'a' ? 'n' : 'a';
        if ($horario){
            $horario->enable($state);
            $horario->save();
            return response($horario->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            [
                'horario_inicio' => 'required|date',
                'horario_fin' => 'required|date|after:horario_inicio',
                'dia' => 'required|max:10',
                'activo_horario' => 'required|in:a,n'
            ],
            ['max' => ' El :attribute no debe exceder los :max caracteres.',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'required'=> 'El :attribute debe ser obligatorio',
                'date'=> 'El :attribute debe ser una hora'
            ]
        );
    }

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(Schedule::class)->make();
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
                Schedule::create($model);
            else
                $errors->add(['error' => $valid[1]]);
        }
        return response('{"message": "Congratulations Prosseced Portals!!!!!!!!!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }
    /**
     * por medio de este metodo genera automaticamente la cantidad de horarios que le ingrese por parametro
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
