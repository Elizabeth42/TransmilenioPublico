<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'rutas';
    protected $primaryKey = 'id_ruta';
    public $timestamps = false;
    protected $fillable = ['codigo_ruta','activo_ruta'];

    //permitira saber si un id de vagon se encuentra asociada a la ruta
    public function hasWagon(int $vagon)
    {
        return $this->wagons()->whereRaw('"PARADAS"."ID_VAGON"='.$vagon)->count() > 0;
    }

    public function wagons(){
        return $this->belongsToMany('App\Wagon', 'paradas', 'id_ruta', 'id_vagon');
    }

    public function timeRouteAssignment(){
        return $this->hasMany('App\TimeRouteAssignment', 'id_ruta', 'id_ruta');
    }


    // permitira activar o desactivar todos los dependientes de rutas en este caso paradas y TimeRouteAssignment
    public function enable($enable){
        $this->activo_ruta = $enable;
        $this->save();
        $wagons = $this->wagons()->get();
        foreach($wagons as $wagon){
            $this->wagons()->updateExistingPivot($wagon->id_vagon, ['estado_parada'=>$enable]);
        }
        $assignaments = $this->timeRouteAssignment()->get();
        foreach ($assignaments as $assignament) {
            $assignament->enable($enable);
        }
    }
}
