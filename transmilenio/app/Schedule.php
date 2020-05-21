<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Schedule extends Model
{
    protected $table = 'horarios';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;
    protected $fillable = ['horario_inicio','horario_fin','dia','activo_horario'];

    public function timeRouteAssignment(){
        return $this->hasMany('App\TimeRouteAssignment', 'id_horario', 'id_horario');
    }

    public function getDuration( $value ) {
        if(!isset($value))
            return null;
        return ($value instanceof \DateTime || $value instanceof \Illuminate\Support\Carbon) ?
            $value->format('H:i:s'): \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('H:i:s');
    }

    // permitira activar o desactivar todos los dependientes de horario en este caso TimeRouteAssignment
    public function enable($enable){
        $this->activo_horario = $enable;
        $this->save();
        if ($enable == 'n'){
            $assignaments = $this->timeRouteAssignment()->get();
            foreach ($assignaments as $assignament) {
                $assignament->enable($enable);
            }
        }
    }
}
