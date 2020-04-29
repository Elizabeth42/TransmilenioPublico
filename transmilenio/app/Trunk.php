<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trunk extends Model
{
    protected $table = 'troncales';
    protected $primaryKey = 'id_troncal';
    public $timestamps = false;
    protected $fillable = ['nombre_troncal','letra_troncal','color_troncal','activo_troncal'];

    public function hasStation(int $station)
    {
        return $this->stations()->whereRaw('"TRONCAL_ESTACION"."ID_ESTACION"='.$station)->count() > 0;
    }

    public function stations(){
        return $this->belongsToMany('App\Station', 'troncal_estacion', 'id_troncal', 'id_estacion');
    }

    public function  portals(){
        return $this->hasMany('App\Portal', 'id_troncal', 'id_portal');
    }
}
