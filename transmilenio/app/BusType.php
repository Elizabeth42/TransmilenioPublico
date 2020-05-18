<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusType extends Model
{
    protected $table = 'tipo_bus';
    protected $primaryKey = 'id_tipo_bus';
    public $timestamps = false;
    protected $fillable = ['nombre_tipo','color','activo_tipo_bus'];

    public function bus(){
        return $this->hasMany('App\Bus', 'id_tipo_bus', 'id_tipo_bus');
    }

    // permitira activar o desactivar todos los dependientes de busType en este caso bus
    public function enable($enable){
        $this->activo_tipo_bus = $enable;
        $this->save();
        if ($enable == 'n'){
            $buses = $this->bus()->get();
            foreach ($buses as $bus) {
                $bus->enable($enable);
            }
        }
    }
}
