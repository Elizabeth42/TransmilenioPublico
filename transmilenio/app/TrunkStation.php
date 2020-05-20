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
        return $this->belongsTo('App\Trunk','id_troncal' ,'id_troncal');
    }

    public function  stations(){
        return $this->belongsTo('App\Station','id_estacion', 'id_estacion');
    }

    public function  wagons(){
        return $this->hasMany('App\Wagon', 'id_troncal_estacion', 'id_troncal_estacion');
    }
    public function  hasNumberWagon(int $numeroVagon){
        return $this->wagons()->where('numero_vagon','=',$numeroVagon)->count() >0;
    }

    // permite activar o desactivar los dependientes de troncales estacion, en este caso los vagones
    public function enable($enable){
        $this->activo_troncal_estacion = $enable;
        $this->save();
        if ($enable == 'n'){
            $wagons = $this->wagons()->get();
            foreach ($wagons as $wagon) {
                $wagon->enable($enable);
            }
        }
    }
}
