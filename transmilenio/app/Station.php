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
        return $this->trunk_stations()->whereRaw('"TRONCAL_ESTACION"."ID_TRONCAL"='.$troncal)->count() > 0;
    }

    public function trunk_stations(){
        return $this->hasMany('App\TrunkStation', 'id_estacion', 'id_estacion');
    }

    // permitira activar o desactivar todos los dependientes de estacion en este caso troncal estacion
    public function enable($enable){
        $this->activo_estacion = $enable;
        $this->save();
        $trunks_stations = $this->trunk_stations()->get();
        foreach ($trunks_stations as $trunk_station) {
            $trunk_station->enable($enable);
        }
    }
}
