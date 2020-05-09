<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeRouteAssignment extends Model
{
    protected $table = 'asignacion_ruta_horario';
    protected $primaryKey = 'id_asignacion_ruta';
    public $timestamps = false;
    protected $fillable = ['id_ruta','id_bus','id_horario','fecha_inicio_operacion','fecha_fin_operacion','activo_asignacion'];

    public function  schedules(){
        return $this->belongsTo('App\Schedule', $ownerKey='id_horario');
    }

    public function  buses(){
        return $this->belongsTo('App\Bus', $ownerKey='id_bus');
    }

    public function  routes(){
        return $this->belongsTo('App\Routes', $ownerKey='id_ruta');
    }
}
