<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrunkStation extends Model
{
    protected $table = 'troncal_estacion';
    protected $primaryKey = 'id_troncal_estacion';
    public $timestamps = false;
    protected $fillable = ['id_estacion','id_troncal','activo_troncal_estacion'];

    public function  trunks(){
        return $this->belongsTo('App\Trunk', $ownerKey='id_troncal');
    }

    public function  stations(){
        return $this->belongsTo('App\Station', $ownerKey='id_estacion');
    }
}
