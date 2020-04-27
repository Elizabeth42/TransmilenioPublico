<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'rutas';
    protected $primaryKey = 'id_ruta';
    public $timestamps = false;
    protected $fillable = ['codigo_ruta'];
}
