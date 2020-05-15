<?php

namespace App\Http\Controllers;

use App\Bus;
use App\Route;
use App\Schedule;
use App\TimeRouteAssignment;
use Illuminate\Http\Request;
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
            return TimeRouteAssignment::where('activo_asignacion', '=', $active)->get();
        }
        return TimeRouteAssignment::all();
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

        //es necesario garantizar que esas tres no se encuentren ya asociadas en la tabla asignacion_ruta_horario
        $exist = TimeRouteAssignment::where('id_ruta', '=', $request->input('id_ruta'))
            ->where('id_bus', '=', $request->input('id_bus'))
            ->where('id_horario', '=', $request->input('id_horario'))
            ->whereDate('fecha_inicio_operacion', '=', $request->input('fecha_inicio_operacion'))->count();
        if($exist>0)
            return response('{"error": "la ruta, el horario, el bus y la fecha de inicio ya fueron asignadas"}', 300)->header('Content-Type', 'application/json');
        // se procede con la creacion de la asignacion
        $created = TimeRouteAssignment::create($validator->validated());
        return response('{ "id": ' . $created->id_asignacion_ruta . '}', 200)->header('Content-Type', 'application/json');
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
            return response('{"error": "La asignacion no existe"}', 300)->header('Content-Type', 'application/json');
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
            return response('{"error": "La asignacion no existe"}', 300)->header('Content-Type', 'application/json');

        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
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
                return response('{"error": "la ruta, el horario, el bus y la fecha de inicio ya fueron asignadas"}', 300)->header('Content-Type', 'application/json');
        }
        if ($asignacion->isDirty('activo_asignacion')){
            $asignacion->enable($request->input('activo_asignacion'));
        }
        if ($asignacion){
            $asignacion->save();
            return response($asignacion->toJson(), 200)->header('Content-Type', 'application/json');
        }
        else{
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TimeRouteAssignment  $timeRouteAssignment
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeRouteAssignment $timeRouteAssignment)
    {
        //
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['activo_asignacion' => 'required|in:a,n',
                'fecha_inicio_operacion' =>'required|date',
                'fecha_fin_operacion' =>'date|after:fecha_inicio_operacion',
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
                'after' => 'La fecha final debe ser posterior a la fecha de inicio'
            ]
        );
    }
}
