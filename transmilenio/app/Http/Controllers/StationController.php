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
        return response(Station::all()->toJson(), 200)->header('Content-Type', 'application/json');
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
        $validator = $this->custom_validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        }
        $created = Station::create($validator->validated());
        return response('{ "id": ' . $created->id_estacion . '}', 200)->header('Content-Type', 'application/json');
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
            return response('{"error": "La estacion no existe"}', 300)->header('Content-Type', 'application/json');
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
            return response('{"error": "La estacion no existe"}', 300)->header('Content-Type', 'application/json');
        $validator = $this->custom_validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->toJson(), 300)->header('Content-Type', 'application/json');
        }
        $updated = $station->update($validator->validated());
        if ($updated)
            return response($station->toJson(), 200)->header('Content-Type', 'application/json');
        else
            return response('{"error": "unknow"}', 500)->header('Content-Type', 'application/json');
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
            return response('{"error": "La estacion no existe"}', 300)->header('Content-Type', 'application/json');
        if ($station->trunks()->count() > 0)
            return response('{ "error": "La estacion tiene troncales asociadas y no puede ser eliminada"}', 300)->header('Content-Type', 'application/json');
        try {
            $deleted = $station->delete();
        } catch (Exception $e) {
            $deleted = false;
        }
        if ($deleted)
            return response('{ "success": "La estacion fue eliminada"}', 200)->header('Content-Type', 'application/json');
        else
            return response('{ "error": "La estacion no pudo ser eliminada"}', 300)->header('Content-Type', 'application/json');
    }

    private function custom_validator($data)
    {
        return Validator::make($data,
            ['nombre_estacion' => 'required|max:50'],
            ['max' => ' El :attribute no debe exceder los :max caracteres.']
        );
    }
}
