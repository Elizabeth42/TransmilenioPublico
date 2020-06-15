<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    protected $table = 'viaje_realizado';
    protected $primaryKey = 'id_viaje';
    public $timestamps = false;
    protected $fillable = ['id_asignacion_ruta','fecha_inicio_viaje','fecha_fin_viaje'];

    public function  asinnations(){
        return $this->belongsTo('App\TimeRouteAssignment', 'id_asignacion_ruta', 'id_asignacion_ruta')->with('schedules')->with('buses')->with('routes');
    }
}
