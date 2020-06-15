<?php

namespace App\Http\Controllers;

use App\Platform;
use App\TrunkStation;
use App\Wagon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            return Wagon::with('trunk_station')->with('platform')->where('activo_vagon', '=', $active)->get();
        }
        return Wagon::with('trunk_station')->with('platform')->get();
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
        $created = Wagon::create($valid[1]);
        return response($created->toJson(), 200)->header('Content-Type', 'application/json');
    }

    private function validateModel($model){
        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($model);
        if ($validator->fails()) {
            return [false, $validator->errors()->toJson()];
        }
        $trunk_station = key_exists('id_troncal_estacion', $model) ? TrunkStation::find($model['id_troncal_estacion']): null;
        $plataforma = key_exists('id_plataforma', $model) ? Platform::find($model['id_plataforma']): null;
        // permitira validar que solamente venga activo una de las foreign key
        if ($trunk_station!=null && $plataforma!=null){
            return [false, 'El vagon solamente puede ser asignado a una troncal_estacion o a una plataforma pero no a ambas'];
        }
        // permitira validar que almenos una de las foreign key venga activa
        if ($trunk_station==null && $plataforma ==null){
            return [false , 'El vagon solamente necesita una troncal_estacion o una plataforma para ser asignado'];
        }
        // en caso de que venga una troncal estacion
        if ($trunk_station!=null){
            if(!isset($trunk_station)){
                return [false, 'La troncal_estacion no existe'];
            }
            if ($trunk_station->activo_troncal_estacion=='n'){
                return [false, 'la troncal_estacion '.$trunk_station->getKey().' se encuentra inactiva'];
            }
            //finalmente se requiere garantizar que esa troncal_estacion no tenga asignada ya este numero de vagon
            if ($trunk_station->hasNumberWagon($model['numero_vagon'])>0){
                return [false, 'la troncal_estacion '.$trunk_station->getKey().' ya tiene ese numero de vagon asociado'];
            }
        }
        // en caso de que venga una plataforma
        if ($plataforma!=null){
            if(!isset($plataforma)){
                return [false, 'la plataforma no existe'];
            }
            if ($plataforma->activo_plataforma=='n'){
                return [false, 'la plataforma '.$plataforma->getKey().' se encuentra inactiva'];
            }
            //finalmente se requiere garantizar que esa plataforma no tenga asignada ya este numero de vagon
            if ($plataforma->hasNumberWagon($model['numero_vagon'])>0){
                return [false, 'la plataforma '.$plataforma->getKey().' ya tiene esa numero de vagon asociado'];
            }
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
        $vagon = Wagon::find($id);
        if (!isset($vagon))
            return response('{"errors":"El vagon no existe"}',
                400)->header('Content-Type', 'application/json');
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
            return response('{"errors":"El vagon no existe"}', 400)->header('Content-Type', 'application/json');

        // permitira validar el request que ingreso que cumpla con las reglas basicas definidas
        $validator = $this->custom_validator($request->all());
        if ($validator->fails()) {
            return response('{"errors": '. $validator->errors()->toJson().'}',  400)->header('Content-Type', 'application/json');
        }
        $vagon->fill($validator->validated());
        $trunk_station = TrunkStation::find($vagon->id_troncal_estacion);
        $plataforma = Platform::find($vagon->id_plataforma);

        if($vagon->isDirty('id_troncal_estacion')&&$vagon->id_troncal_estacion!=null){
            if(!isset($trunk_station)){
                return response('{"errors":"la troncal_estacion no existe"}', 400)->header('Content-Type', 'application/json');
            }
            if($trunk_station->activo_troncal_estacion == 'n'){
                return response('{"errors":"la troncal_estacion se encuentra inactiva"}', 400)->header('Content-Type', 'application/json');
            }

        }
        if ($vagon->isDirty('id_plataforma')&&$vagon->id_plataforma !=null){
            if(!isset($plataforma)){
                return response('{"errors":"la plataforma no existe"}', 400)->header('Content-Type', 'application/json');
            }
            if($plataforma->activo_plataforma == 'n'){
                return response('{"errors":"la plataforma se encuentra inactiva"}', 400)->header('Content-Type', 'application/json');
            }
        }
        // si viene nulo desde el request no deberia ser tenido en cuenta el valor anterior
        if($request->input('id_plataforma')==null){
            $vagon->id_plataforma=null;
        }
        if($request->input('id_troncal_estacion')==null){
            $vagon->id_troncal_estacion=null;
        }
        // permitira validar que solamente venga activo una de las foreign key
        if ($vagon->id_troncal_estacion!=null &&  $vagon->id_plataforma!=null){
            return response('{"errors":"El vagon solamente puede ser asignado a una troncal_estacion o a una plataforma pero no a ambas"}',
                400)->header('Content-Type', 'application/json');
        }
        // permitira validar que almenos una de las foreign key venga activa
        if ($vagon->id_troncal_estacion==null &&  $vagon->id_plataforma==null){
            return response('{"errors":"El vagon solamente necesita una troncal_estacion o una plataforma para ser asignado"}',
                400)->header('Content-Type', 'application/json');
        }
        // en caso de que venga una troncal estacion
        if ($vagon->id_troncal_estacion!=null){
            if ($vagon->isDirty('numero_vagon')){
                //finalmente se requiere garantizar que esa troncal_estacion no tenga asignada ya este numero de vagon
                if ($trunk_station->hasNumberWagon($request->input('numero_vagon'))){
                    return response('{"errors":"la troncal_estacion ya tiene ese numero de vagon asociado"}',
                        400)->header('Content-Type', 'application/json');
                }
            }
        }
        // en caso de que venga una troncal estacion
        if ( $vagon->id_plataforma!=null){
            if ($vagon->isDirty('numero_vagon')){
                //finalmente se requiere garantizar que esa troncal_estacion no tenga asignada ya este numero de vagon
                if ($plataforma->hasNumberWagon($request->input('numero_vagon'))){
                    return response('{"errors":"la plataforma ya tiene esa numero de vagon asociado"}',
                        400)->header('Content-Type', 'application/json');
                }
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
        $vagon = Wagon::find($id);
        if (!isset($vagon))
            return response('{"errors":"El vagon no existe"}', 400)->header('Content-Type', 'application/json');
        $troncalEstacion = TrunkStation::find($vagon->id_troncal_estacion);
        $plataforma = Platform::find($vagon->id_plataforma);
        if($troncalEstacion != null){
            if ($troncalEstacion->activo_troncal_estacion == 'n')
                return response('{"errors":"La troncal_estacion se encuentra inactiva"}', 400)->header('Content-Type', 'application/json');
        }else{
            if ($plataforma->activo_plataforma == 'n')
                return response('{"errors":"La plataforma se encuentra inactiva"}', 400)->header('Content-Type', 'application/json');
        }
        $state = $vagon->activo_vagon == 'a' ? 'n' : 'a';
        if ($vagon){
            $vagon->enable($state);
            $vagon->save();
            return response($vagon->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            [
                'numero_vagon'=>'required|integer',
                'activo_vagon' => 'required|in:a,n',
                'id_plataforma'=>'nullable|integer',
                'id_troncal_estacion'=>'nullable|integer'
//                'id_plataforma'=>[
//                    Rule::exists('plataformas', '')->where(function ($query) {
//                        $query->where('activo_plataforma', 'a');
//                    })
//                ],
//                'id_troncal_estacion'=>[
//                    Rule::exists('troncal_estacion', '')->where(function ($query) {
//                        $query->where('activo_troncal_estacion', 'a');
//                    })
//                ],

            ],
            ['required'=> 'El :attribute es obligatorio',
//                'id_plataforma.exists'=>'La plataforma no existe o no esta activa',
//                'id_troncal_estacion.exists'=>'La troncal_estacion no existe o no esta activa',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'integer'=> 'El :attribute debe ser de tipo entero']
        );
    }

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(Wagon::class)->make();
//            $valid = \WagonSeed::validate($model);
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
                Wagon::create($model);
            else
                $errors->add($valid[1]);
        }
        return response('{"message": "Congratulations!!!!!!!!!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * por medio de este metodo genera automaticamente la cantidad de vagones que le ingrese por parametro
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
                'Content-disposition' => 'attachment; filename=Wagon'.$amount.'Random.json'
            ]);
    }
}
