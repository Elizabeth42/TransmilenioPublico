<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $table = 'plataformas';
    protected $primaryKey = 'id_plataforma';
    public $timestamps = false;
    protected $fillable = ['id_portal','numero_plataforma','activo_plataforma'];

    public function  wagons(){
        return $this->hasMany('App\Wagon', 'id_plataforma', 'id_plataforma');
    }
    public function  Portals(){
        return $this->belongsTo('App\Portal', $ownerKey='id_portal');
    }

    // permitira activar o desactivar los vagones a partir del estado de la plataforma
    public function enable($enable){
        $this->activo_plataforma = $enable;
        $this->save();
        $wagons = $this->wagons()->get();
        foreach ($wagons as $wagon) {
            $wagon->enable($enable);
        }
    }
}
