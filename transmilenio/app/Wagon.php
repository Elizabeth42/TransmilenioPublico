<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wagon extends Model
{
    protected $table = 'vagones';
    protected $primaryKey = 'id_vagon';
    public $timestamps = false;
    protected $fillable = ['id_plataforma','id_troncal_estacion','numero_vagon','activo_vagon'];

    public function  platform(){
        return $this->belongsTo('App\Platform', 'id_plataforma', 'id_plataforma')->with('portal');
    }

    public function trunk_station(){
        return $this->belongsTo('App\TrunkStation', 'id_troncal_estacion', 'id_troncal_estacion')->with('trunk')->with('station');
    }

    //permitira saber si un id de ruta se encuentra asociada al vagon
    public function hasRoute(int $ruta)
    {
        return $this->routes()->whereRaw('"PARADAS"."ID_RUTA"='.$ruta)->count() > 0;
    }

    public function routes(){
        return $this->belongsToMany('App\Route', 'paradas', 'id_vagon', 'id_ruta');
    }
    public function enable($enable){
        $this->activo_vagon = $enable;
        $this->save();
        if ($enable == 'n'){
            $routes = $this->routes()->get();
            foreach($routes as $route){
                $this->routes()->updateExistingPivot($route->id_ruta, ['estado_parada'=>$enable]);
            }
        }
    }
}
