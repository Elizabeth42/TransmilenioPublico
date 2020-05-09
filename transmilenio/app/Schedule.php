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
}
