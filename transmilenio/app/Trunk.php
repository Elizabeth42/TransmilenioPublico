<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Boolean;

class Trunk extends Model
{
    protected $table = 'troncales';
    protected $primaryKey = 'id_troncal';
    public $timestamps = false;
    protected $fillable = ['nombre_troncal','letra_troncal','color_troncal','activo_troncal'];

    public function trunk_stations(){
        return $this->hasMany('App\TrunkStation', 'id_troncal', 'id_troncal');
    }

    public function portals(){
        return $this->hasMany('App\Portal', 'id_troncal', 'id_troncal');
    }

    public function hasStation(int $station)
    {
        return $this->trunk_stations()->whereRaw('"TRONCAL_ESTACION"."ID_ESTACION"='.$station)->count() > 0;
    }

    // metodo para desactivar o activar portales y troncal estacion
    public function enable($enable){
        $this->activo_troncal = $enable;
        $this->save();
        if ($enable == 'n'){
            $trunks_stations = $this->trunk_stations()->get();
            foreach ($trunks_stations as $trunk_station) {
                $trunk_station->enable($enable);
            }
            $portals = $this->portals()->get();
            foreach ($portals as $portal) {
                $portal->enable($enable);
            }
        }
    }
}
