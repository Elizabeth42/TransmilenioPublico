<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $table = 'plataformas';
    protected $primaryKey = 'id_plataforma';
    public $timestamps = false;
    protected $fillable = ['id_portal','id_estacion','id_troncal','numero_plataforma','activo_plataforma'];

    public function  wagons(){
        return $this->hasMany('App\Wagon', 'id_plataforma', 'id_vagon');
    }
    public function  Portals(){
        return $this->belongsTo('App\Portal', $ownerKey='id_portal');
    }
}
