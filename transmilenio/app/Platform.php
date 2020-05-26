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
    public function  portal(){
        return $this->belongsTo('App\Portal', 'id_portal', 'id_portal');
    }
    public function  hasNumberWagon(int $numeroVagon){
        return $this->wagons()->where('numero_vagon','=',$numeroVagon)->count() >0;
    }

    // permitira activar o desactivar los vagones a partir del estado de la plataforma
    public function enable($enable){
        $this->activo_plataforma = $enable;
        $this->save();
        if ($enable == 'n'){
            $wagons = $this->wagons()->get();
            foreach ($wagons as $wagon) {
                $wagon->enable($enable);
            }
        }
    }
}
