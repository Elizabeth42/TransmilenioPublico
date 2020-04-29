<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $table = 'plataformas';
    protected $primaryKey = 'id_plataforma';
    public $timestamps = false;
    protected $fillable = ['id_portal','id_estacion','id_troncal','numero_plataforma','activo_plataforma'];

    public function troncal(){
        return $this->hasOne('App\Trunk', 'id_troncal', 'id_troncal');
    }

    public function station(){
        return $this->hasOne('App\Station', 'id_estacion', 'id_estacion');
    }

    public function  wagons(){
        return $this->hasMany('App\Wagon', 'id_plataforma', 'id_vagon');
    }
}
