<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wagon extends Model
{
    protected $table = 'vagones';
    protected $primaryKey = 'id_vagon';
    public $timestamps = false;
    protected $fillable = ['id_plataforma','id_troncal_estacion','numero_vagon','activo_vagon'];

    public function  platforms(){
        return $this->belongsTo('App\Platform', $ownerKey='id_plataforma');
    }


}
