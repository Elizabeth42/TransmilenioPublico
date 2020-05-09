<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusType extends Model
{
    protected $table = 'tipo_bus';
    protected $primaryKey = 'id_tipo_bus';
    public $timestamps = false;
    protected $fillable = ['nombre_tipo','color','activo_tipo_bus'];

    public function bus(){
        return $this->hasMany('App\Bus', 'id_tipo_bus', 'id_tipo_bus');
    }
}
