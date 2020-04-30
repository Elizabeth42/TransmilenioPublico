<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    protected $table = 'portales';
    protected $primaryKey = 'id_portal';
    public $timestamps = false;
    protected $fillable = ['nombre_portal','id_troncal','activo_portal'];

    public function  trunks(){
        return $this->belongsTo('App\Trunk', $ownerKey='id_troncal');
    }
    public function  platforms(){
        return $this->hasMany('App\Platform', 'id_portal', 'id_portal');
    }
}
