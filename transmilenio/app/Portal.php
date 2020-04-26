<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    protected $table = 'portales';
    protected $primaryKey = 'id_portal';
    public $timestamps = false;
    protected $fillable = ['nombre_portal','id_troncal'];

    public function  trunks(){
        return $this->belongsTo('App\Trunk', $ownerKey='id_troncal');
    }
}
