<?php

namespace App\Http\Controllers;

use App\Platform;
use App\TrunkStation;
use App\Wagon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WagonController extends Controller
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
            return Wagon::where('activo_vagon', '=', $active)->get();
        }
        return Wagon::all();
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
        if ($validator->fails()) {
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        }
        $trunk_station = TrunkStation::find($request->input('id_troncal_estacion'));
        $plataforma = Platform::find($request->input('id_plataforma'));
        // permitira validar que solamente venga activo una de las foreign key
        if ($trunk_station!=null && $plataforma!=null){
            return response('{"error": "El vagon solamente puede ser asignado a una troncal_estacion o a una plataforma pero no a ambas"}', 300)->header('Content-Type', 'application/json');
        }
        // permitira validar que almenos una de las foreign key venga activa
        if ($trunk_station==null && $plataforma ==null){
            return response('{"error": "El vagon solamente necesita una troncal_estacion o una plataforma para ser asignado"}', 300)->header('Content-Type', 'application/json');
        }
        // en caso de que venga una troncal estacion
        if ($trunk_station!=null){
            //finalmente se requiere garantizar que esa troncal_estacion no tenga asignada ya este numero de vagon
            if ($trunk_station->hasNumberWagon($request->input('numero_vagon'))){
                return response('{"error": "la troncal_estacion ya tiene ese numero de vagon asociado"}', 300)->header('Content-Type', 'application/json');
            }
        }
        // en caso de que venga una troncal estacion
        if ($plataforma!=null){
            //finalmente se requiere garantizar que esa troncal_estacion no tenga asignada ya este numero de vagon
            if ($plataforma->hasNumberWagon($request->input('numero_vagon'))){
                return response('{"error": "la plataforma ya tiene esa numero de vagon asociado"}', 300)->header('Content-Type', 'application/json');
            }
        }
        //se encargara de crear el vagon con la informacion del json
        $created = Wagon::create($validator->validated());
        return response($created->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vagon = Wagon::find($id);
        if (!isset($vagon))
            return response('{"error": "El vagon no existe"}', 300)->header('Content-Type', 'application/json');
        return response($vagon->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Wagon  $wagon
     * @return \Illuminate\Http\Response
     */
    public function edit(Wagon $wagon)
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
        $vagon = Wagon::find($id);
        if (!isset($vagon))
            return response('{"error": "El vagon no existe"}', 300)->header('Content-Type', 'application/json');

        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        }

        $vagon->fill($validator->validated());

        //es necesario saber si se esta modificando de una plataforma a troncal_Estacion o viceversa
        if($vagon->isDirty('id_troncal_estacion'))
            $vagon->id_plataforma = null;
        elseif ($vagon->isDirty('id_plataforma'))
            $vagon->id_troncal_estacion=null;
        //asignacion de troncal estacion y de plataforma
        $trunk_station = TrunkStation::find($vagon->id_troncal_estacion);
        $plataforma = Platform::find($vagon->id_plataforma);

        //$plataforma = Platform::find($request->input('id_plataforma'));
        // permitira validar que solamente venga activo una de las foreign key
        if ($trunk_station!=null && $plataforma!=null){
            return response('{"error": "El vagon solamente puede ser asignado a una troncal_estacion o a una plataforma pero no a ambas"}', 300)->header('Content-Type', 'application/json');
        }
        // permitira validar que almenos una de las foreign key venga activa
        if ($trunk_station==null && $plataforma ==null){
            return response('{"error": "El vagon solamente necesita una troncal_estacion o una plataforma para ser asignado"}', 300)->header('Content-Type', 'application/json');
        }
        // en caso de que venga una troncal estacion
        if ($trunk_station!=null && $vagon->isDirty('numero_vagon')){
            //finalmente se requiere garantizar que esa troncal_estacion no tenga asignada ya este numero de vagon
            if ($trunk_station->hasNumberWagon($request->input('numero_vagon'))){
                return response('{"error": "la troncal_estacion ya tiene ese numero de vagon asociado"}', 300)->header('Content-Type', 'application/json');
            }
        }
        // en caso de que venga una troncal estacion
        if ($plataforma!=null && $vagon->isDirty('numero_vagon')){
            //finalmente se requiere garantizar que esa troncal_estacion no tenga asignada ya este numero de vagon
            if ($plataforma->hasNumberWagon($request->input('numero_vagon'))){
                return response('{"error": "la plataforma ya tiene esa numero de vagon asociado"}', 300)->header('Content-Type', 'application/json');
            }
        }

        //esto es para establecer si los hijos se activan o inactivan
        if ($vagon->isDirty('activo_vagon')){
            $vagon->enable($request->input('activo_vagon'));
        }
        if ($vagon){
            $vagon->save();
            return response($vagon->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
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
        $vagon = Wagon::find($id);
        if (!isset($vagon))
            return response('{"error": "El vagon no existe"}', 300)->header('Content-Type', 'application/json');
        $troncalEstacion = TrunkStation::find($vagon->id_troncal_estacion);
        $plataforma = Platform::find($vagon->id_plataforma);
        if($troncalEstacion != null){
            if ($troncalEstacion->activo_troncal_estacion == 'n')
                return response('{"error": "La troncal_estacion se encuentra inactiva"}', 300)->header('Content-Type', 'application/json');
        }else{
            if ($plataforma->activo_plataforma == 'n')
                return response('{"error": "La plataforma se encuentra inactiva"}', 300)->header('Content-Type', 'application/json');
        }
        $state = $vagon->activo_vagon == 'a' ? 'n' : 'a';
        if ($vagon){
            $vagon->enable($state);
            $vagon->save();
            return response($vagon->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            [
                'numero_vagon'=>'required|integer',
                'activo_vagon' => 'required|in:a,n',
                'id_plataforma'=>[
                    Rule::exists('plataformas', '')->where(function ($query) {
                        $query->where('activo_plataforma', 'a');
                    })
                ],
                'id_troncal_estacion'=>[
                    Rule::exists('troncal_estacion', '')->where(function ($query) {
                        $query->where('activo_troncal_estacion', 'a');
                    })
                ],

            ],
            ['required'=> 'El :attribute es obligatorio',
                'id_plataforma.exists'=>'La plataforma no existe o no esta activa',
                'id_troncal_estacion.exists'=>'La troncal_estacoion no existe o no esta activa',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'integer'=> 'El :attribute debe ser de tipo entero']
        );
    }
}
