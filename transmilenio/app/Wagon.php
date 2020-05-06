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

    public function trunk_station(){
        return $this->belongsTo('App\TrunkStation', 'id_troncal_estacion', 'id_troncal_estacion');
    }

    public function enable($enable){
        $this->activo_vagon = $enable;
        $this->save();
        /*$wagons = $this->wagons()->get();
        foreach ($wagons as $wagon) {
            $wagon->enable($enable);
        }*/
    }

}
