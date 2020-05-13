<?php

namespace App\Http\Controllers;

use App\TimeRouteAssignment;
use Illuminate\Http\Request;
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TimeRouteAssignment  $timeRouteAssignment
     * @return \Illuminate\Http\Response
     */
    public function show(TimeRouteAssignment $timeRouteAssignment)
    {
        //
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
     * @param  \App\TimeRouteAssignment  $timeRouteAssignment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TimeRouteAssignment $timeRouteAssignment)
    {
        //
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
                'fecha_fin_operacion' =>'required|date',
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
                'date'=> 'El :attribute debe ser una fecha'
            ]
        );
    }
}
