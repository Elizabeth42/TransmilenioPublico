<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $table = 'buses';
    protected $primaryKey = 'id_bus';
    public $timestamps = false;
    protected $fillable = ['placabus','activo_bus','id_tipo_bus'];

    public function  busType(){
        return $this->belongsTo('App\BusType', 'id_tipo_bus', 'id_tipo_bus');
    }

    public function timeRouteAssignment(){
        return $this->hasMany('App\TimeRouteAssignment', 'id_bus', 'id_bus');
    }

    // permitira activar o desactivar todos los dependientes de bus en este caso TimeRouteAssignment
    public function enable($enable){
        $this->activo_bus = $enable;
        $this->save();
        if ($enable == 'n'){
            $assignaments = $this->timeRouteAssignment()->get();
            foreach ($assignaments as $assignament) {
                $assignament->enable($enable);
            }
        }
    }
}
