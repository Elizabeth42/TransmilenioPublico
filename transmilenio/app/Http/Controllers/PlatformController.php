<?php

namespace App\Http\Controllers;

use App\Platform;
use App\Portal;
use App\Station;
use App\Trunk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlatformController extends Controller
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
            return Platform::where('activo_plataforma', '=', $active)->get();
        }
        return Platform::all();
     //   return response(Platform::all()->toJson(), 200)->header('Content-Type', 'application/json');
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
        $valid = $this->validateModel($request->all());
        if(!$valid[0])
            return response('{"errors":"'.$valid[1].'"}', 400)->header('Content-Type', 'application/json');
        //se encargara de crear la plataforma con la informacion del json
        $created = Platform::create($valid[1]);
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
        $platform = Platform::find($id);
        if (!isset($platform))
            return response('{"errors":"La plataforma no existe"}', 400)->header('Content-Type', 'application/json');
        return response($platform->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Platform  $platform
     * @return \Illuminate\Http\Response
     */
    public function edit(Platform $platform)
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
        $platform = Platform::find($id);
        if (!isset($platform))
            return response('{"errors":"La plataforma no existe"}', 400)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response('{"errors": '. $validator->errors()->toJson().'}',  400)->header('Content-Type', 'application/json');

        $updated = $platform->update($validator->validated());
        //esto es para establecer si los hijos se activan o inactivan
        if ($platform->wasChanged('activo_plataforma')){
            $platform->enable($request->input('activo_plataforma'));
        }
        if ($updated)
            return response($platform->toJson(), 200)->header('Content-Type', 'application/json');
        else
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $platform = Platform::find($id);
        if (!isset($platform))
            return response('{"errors":"La plataforma no existe"}', 400)->header('Content-Type', 'application/json');
        $portal = Portal::find($platform->id_portal);
        if ($portal->activo_portal == 'n')
            return response('{"errors":"El portal se encuentra inactivo"}', 400)->header('Content-Type', 'application/json');
        $state = $platform->activo_plataforma == 'a' ? 'n' : 'a';
        if ($platform){
            $platform->enable($state);
            $platform->save();
            return response($platform->toJson(), 200)->header('Content-Type', 'application/json');
        }else{
            return response('{"errors":"unknow"}', 500)->header('Content-Type', 'application/json');
        }
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['numero_plataforma' => 'required|max:2',
                'activo_plataforma' => 'required|in:a,n',
                //con esto valido que la troncal sea obligatoria, que la troncal exista y que se encuentre activa
                'id_portal'=>['required','integer',
                    Rule::exists('portales', '')->where(function ($query) {
                        $query->where('activo_portal', 'a');
                    })
                ]
            ],
            ['required' => ' El :attribute es obligatorio.',
                'max' => ' El :attribute no debe exceder los :max caracteres.',
                'id_portal.exists'=>'El portal no existe o no esta activa',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo',
                'integer'=> 'El :attribute debe ser de tipo entero']
        );
    }

    public function getRandom($amount) {
        $result = collect();
        for ($i = 0; $i < $amount ; $i++) {
            $model = factory(Platform::class)->make();
//            $valid = \PlatformSeed::validate($model);
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
                Platform::create($model);
            else
                $errors->add($valid[1]);
        }
        return response('{"message": "Congratulations Platforms generate!!!!!!!!!", "errors":'.json_encode($errors).'}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * por medio de este metodo genera automaticamente la cantidad de plataformas que le ingrese por parametro
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
                'Content-disposition' => 'attachment; filename=Platform'.$amount.'Random.json'
            ]);
    }
}
