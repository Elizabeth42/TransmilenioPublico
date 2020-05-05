<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $table = 'estaciones';
    protected $primaryKey = 'id_estacion';
    public $timestamps = false;
    protected $fillable = ['nombre_estacion','activo_estacion'];

    public function hasTrunk(int $troncal)
    {
        return $this->trunks()->whereRaw('"troncal_estacion"."id_troncal"='.$troncal)->count() > 0;
    }

    public function trunk_stations(){
        return $this->hasMany('App\TrunkStation', 'id_estacion', 'id_estacion');
    }

//    public function trunks(){
//        return $this->belongsToMany('App\Trunk', 'troncal_estacion', 'id_estacion', 'id_troncal');
//    }
}
