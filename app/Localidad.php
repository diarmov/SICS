<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    protected $table = 'localidades';
    protected $primaryKey = 'id_localidad';
    public $timestamps = false;

    protected $fillable = [
        'id_municipio',
        'nombre',
        'clave',
        'activo',
        'usuario_creacion',
        'usuario_modificacion'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }

    public function comites()
    {
        return $this->hasMany(ComiteVigilancia::class, 'id_localidad', 'id_localidad');
    }
}
