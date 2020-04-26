<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $table = 'estaciones';
    protected $primaryKey = 'id_estacion';
    public $timestamps = false;
    protected $fillable = ['nombre_estacion'];

    public function hasTrunk(int $troncal)
    {
        return $this->trunks()->whereRaw('"TRONCAL_ESTACION"."ID_TRONCAL"='.$troncal)->count() > 0;
    }

    public function trunks(){
        return $this->belongsToMany('App\Trunk', 'troncal_estacion', 'id_estacion', 'id_troncal');
    }
}
