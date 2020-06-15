<?php

namespace App\Http\Controllers;

use App\Bus;
use App\Route;
use App\Schedule;
use App\TimeRouteAssignment;
use App\Travel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TimeRouteAssignmentController extends Controller
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
            return TimeRouteAssignment::with('schedules')->with('buses')->with('routes')->where('activo_asignacion', '=', $active)->get();
        }
        return TimeRouteAssignment::with('schedules')->with('buses')->with('routes')->get();
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
        //se encargara de crear la asignacion con la informacion del json
        $created = TimeRouteAssignment::create($valid[1]);
        return response($created->toJson(), 200)->header('Content-Type', 'application/json');
    }

    private function validateModel($model){
        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($model);
        if ($validator->fails()) {
            return [false, $validator->errors()->toJson()];
        }
        //es necesario garantizar que esas tres no se encuentren ya asociadas en la tabla asignacion_ruta_horario
        $exist = TimeRouteAssignment::where('id_ruta', '=', $model['id_ruta'])
            ->where('id_bus', '=', $model['id_bus'])
            ->where('id_horario', '=', $model['id_horario'])
            ->whereDate('fecha_inicio_operacion', '=', $model['fecha_inicio_operacion'])->count();
        if($exist>0)
            return [false , 'la ruta, el horario, el bus y la fecha de inicio ya fueron asignadas'];
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
        $asignacion = TimeRouteAssignment::find($id);
        if (!isset($asignacion))
            return response('{"errors":"La asignacion no existe"}', 400)->header('Content-Type', 'application/json');
        return response($asignacion->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TimeRouteAssignment  $timeRouteAssignment
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeRouteAssignment $timeRouteAssignment)
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
        $asignacion = TimeRouteAssignment::find($id);
        if (!isset($asignacion))
            return response('{"errors":"La asignacion no existe"}', 400)->header('Content-Type', 'application/json');

        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($request->all());
        if ($validator->fails()) {
            return response('{"errors": '. $validator->errors()->toJson().'}',  400)->header('Content-Type', 'application/json');
        }
        //$updated = $asignacion->update($validator->validated());
        $asignacion->fill($validator->validated());
        //es necesario saber si se esta modificando
        //esto es para establecer si los hijos se activan o inactivan

        if ($asignacion->isDirty('id_ruta')||$asignacion->isDirty('id_bus')||$asignacion->isDirty('id_horario')||$asignacion->isDirty('fecha_inicio_operacion')){
            //es necesario garantizar que esas tres no se encuentren ya asociadas en la tabla asignacion_ruta_horario
            $exist = TimeRouteAssignment::where('id_ruta', '=', $request->input('id_ruta'))
                ->where('id_bus', '=', $request->input('id_bus'))
                ->where('id_horario', '=', $request->input('id_horario'))
                ->whereDate('fecha_inicio_operacion', '=', $request->input('fecha_inicio_operacion'))->count();
            Log::info('el valor es: '.$exist);
            if($exist>0)
                return response('{"errors":"la ruta, el horario, el bus y la fecha de inicio ya fueron asignadas"}', 400)->header('Content-Type', 'application/json');
        }
        if ($asignacion->isDirty('activo_asignacion')){
            $asignacion->enable($request->input('activo_asignacion'));
        }
        if ($asignacion){
            $asignacion->save();
            return response($asignacion->toJson(), 200)->header('Content-Type', 'application/json');
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
        $asignacion = TimeRouteAssignment::find($id);
        if (!isset($asignacion))
            return response('{"errors":"La asignacion no existe"}', 400)->header('Content-Type', 'application/json');
        $ruta = Route::find($asignacion->id_ruta);
        $horario = Schedule::find($asignacion->id_horario);
        $bus = Bus::find($asignacion->id_bus);
        if($ruta->activo_ruta == 'n' || $horario->activo_horario == 'n'|| $bus->activo_bus == 'n'){
            return response('{"errors":"La ruta o el bus o el horario no se encuentra activos"}', 400)->header('Content-Type', 'application/json');
        }
        $state = $asignacion->activo_asignacion == 'a' ? 'n' : 'a';
        if ($asignacion){
            $asignacion->enable($state);
            $asignacion->save();
            return response($asignacion->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['activo_asignacion' => 'required|in:a,n',
                'fecha_inicio_operacion' =>'required|date|before:now',
                'fecha_fin_operacion' =>'date|after:fecha_inicio_operacion|nullable',
                //con esto valido que la ruta sea obligatoria, que la ruta exista y que se encuentre activa
                'id_ruta'=>['required',
                    Rule::exists('rutas', '')->where(function ($query) {
                        $query->where('activo_ruta', 'a');
                    })
                ],
                //con esto valido que el bus sea obligatorio, que el bus exista y que se encuentre activo
                'id_bus'=>['required',
                    Rule::exists('buses')->where(function ($query) {
                        $query->where('activo_bus', 'a');
                    })
                ],
                //con esto valido que el horario sea obligatorio, que el horario exista y que se encuentre activo
                'id_horario'=>['required',
                    Rule::exists('horarios')->where(function ($query) {
                        $query->where('activo_horario', 'a');
                    })
                ],
            ],
            [
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'required'=> 'El :attribute es obligatorio',
                'id_ruta.exists'=>'La ruta no existe o no esta activa',
                'id_bus.exists'=>'El bus no existe o esta inactivo',
                'id_horario.exists'=>'El horario no existe o esta inactivo',
                'date'=> 'El :attribute debe ser una fecha',
                'after' => 'La fecha final debe ser posterior a la fecha de inicio',
                'before' => 'La fecha inicio debe ser previa al momento actual'
            ]
        );
    }

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(TimeRouteAssignment::class)->make();
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
                TimeRouteAssignment::create($model);
            else
                $errors->add($valid[1]);
        }
        return response('{"message": "Congratulations!!!!!!!!!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * por medio de este metodo genera automaticamente la cantidad de asignaciones que le ingrese por parametro
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
                'Content-disposition' => 'attachment; filename=TimeRuteAssignment'.$amount.'Random.json'
            ]);
    }

    /**
     * Permite obtener todas las asignaciones que se encuentren activas, ordenarlas por el id_ruta  y contar
     * la cantidad de viajes que se ha realizado  con esa asignacion
     */
    public function getReport()
    {
        $assignments = TimeRouteAssignment::where('activo_asignacion', '=', 'a')->orderBy('id_ruta')->get();
        $report = collect();
        foreach ($assignments as $key => $assign)
        {
            $element = [];
            $element['route'] = $assign->routes()->first();
            $element['bus'] = $assign->buses()->first();
            $element['schedule'] = $assign->schedules()->first();
            $element['count'] = $assign->travels()->count();
            $report->add($element);
        }
        return $report;
    }

    /**
     *
     * Este metodo permite obtener todos los viajes de cada asignacion por fecha
     * @return \Illuminate\Support\Collection
     */
    public function getReportByDate()
    {
        /*
         *
        $assignments = DB::table('asignacion_ruta_horario')
            ->join('viaje_realizado as vr', 'asignacion_ruta_horario.id_asignacion_ruta', '=', DB::raw('vr."ID_ASIGNACION_RUTA"'))
            ->where('activo_asignacion', '=', 'a')
            ->groupBy('id_asignacion_ruta', 'id_horario', DB::raw('TRUNC(vr."FECHA_INICIO_VIAJE")'))
            ->selectRaw('ID_ASIGNACION_RUTA, ID_HORARIO, TRUNC(vr."FECHA_INICIO_VIAJE"), count(vr."FECHA_INICIO_VIAJE") as COUNT')
            ->get();
        */
        $assignments = Travel::select('id_asignacion_ruta',  DB::raw('TRUNC("FECHA_INICIO_VIAJE") as fecha_inicio_viaje'), DB::raw('COUNT(TRUNC("FECHA_INICIO_VIAJE")) as total'))
            ->groupBy('id_asignacion_ruta', DB::raw('TRUNC("FECHA_INICIO_VIAJE")'))->get();
        /*
         *SELECT ID_ASIGNACION_RUTA, TRUNC(FECHA_INICIO_VIAJE) AS FECHA,COUNT(*) FROM VIAJE_REALIZADO GROUP BY ID_ASIGNACION_RUTA,TRUNC(FECHA_INICIO_VIAJE);
         * */
        /*
         * [ "route" => "hola" , "bus" => "como", "count" => "Estas?"]
         */
        $report = collect();
        foreach ($assignments as $assign)
        {
            $element = [];
            $assign_route = TimeRouteAssignment::find($assign->id_asignacion_ruta);
            $element['route'] = $assign_route->routes()->first();
            $element['bus'] = $assign_route->buses()->first();
            $element['schedule'] = $assign_route->schedules()->first();
            $element['date'] = $assign->fecha_inicio_viaje;
            $element['fecha_inicio_operacion'] = $assign_route->fecha_inicio_operacion;
            $element['fecha_fin_operacion'] = $assign_route->fecha_fin_operacion;
            $element['activo_asignacion'] = $assign_route->activo_asignacion;

            $element['count'] = $assign->total;
            $report->add($element);
        }
        return $report;
    }
}
