<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    protected $table = 'portales';
    protected $primaryKey = 'id_portal';
    public $timestamps = false;
    protected $fillable = ['nombre_portal','id_troncal','activo_portal'];

    public function  trunk(){
        return $this->belongsTo('App\Trunk', 'id_troncal','id_troncal');
    }
    public function  platforms(){
        return $this->hasMany('App\Platform', 'id_portal', 'id_portal');
    }
    // permitira activar o desactivar las plataformas asociadas a este portal
    public function enable($enable){
        $this->activo_portal = $enable;
        $this->save();
        if ($enable == 'n'){
            $platforms = $this->platforms()->get();
            foreach ($platforms as $platform) {
                $platform->enable($enable);
            }
        }
    }
}
