<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'horarios';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;
    protected $fillable = ['horario_inicio','horario_fin','dia','activo_horario'];

    public function timeRouteAssignment(){
        return $this->hasMany('App\TimeRouteAssignment', 'id_horario', 'id_horario');
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
