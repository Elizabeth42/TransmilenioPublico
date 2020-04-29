<?php

namespace App\Http\Controllers;
use App\Portal;
use App\Trunk;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PortalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Portal::all()->toJson(), 200)->header('Content-Type', 'application/json');
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
     * @param int $id
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
    // permitira validar si el id de la troncal existe
        $trunk = Trunk::find($request->input('id_troncal'));
        if (!isset($trunk))
            return response('{"error": "La troncal no existe"}', 300)->header('Content-Type', 'application/json');

        $created = Portal::create($validator->validated());
        return response('{ "id": ' . $created->id_portal . '}', 200)->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $portal = Portal::find($id);
        if (!isset($portal))
            return response('{"error": "El portal no existe"}', 300)->header('Content-Type', 'application/json');
        return response($portal->toJson(), 200)->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $portal = Portal::find($id);
        if (!isset($portal))
            return response('{"error": "El portal no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails())
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        $updated = $portal->update($validator->validated());
        if ($updated)
            return response($portal->toJson(), 200)->header('Content-Type', 'application/json');
        else
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $portal = Portal::find($id);
        if (!isset($portal))
            return response('{"error": "El portal no existe"}', 300)->header('Content-Type', 'application/json');
        try {
            $deleted = $portal->delete();
            log::info(print_r('_______________________________________________________________________________',true));
            //ini_set('memory_limit', '-1');
        } catch (Exception $e) {
            log::info(print_r($e,true));
            $deleted = false;
        }
        if ($deleted)
            return response('{ "success": El portal fue eliminado"}', 200)->header('Content-Type', 'application/json');
        else
            return response('{ "error": "El portal no pudo ser eliminado"}', 300)->header('Content-Type', 'application/json');
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['nombre_portal' => 'required|max:50',
                'activo_portal' => 'required|in:a,n',
                'id_troncal'=>'required'
            ],
            ['max' => ' El :attribute no debe exceder los :max caracteres.',
                'in'=> 'El :attribute no puede tener otro valor que a para activo o n para inactivo']
        );
    }
}
