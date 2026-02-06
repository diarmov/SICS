<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipios';
    protected $primaryKey = 'id_municipio';
    public $timestamps = false;

    protected $fillable = [
        'id_estado',
        'nombre',
        'clave',
        'activo',
        'usuario_creacion',
        'usuario_modificacion'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    public function localidades()
    {
        return $this->hasMany(Localidad::class, 'id_municipio', 'id_municipio');
    }

    public function comites()
    {
        return $this->hasMany(ComiteVigilancia::class, 'id_municipio', 'id_municipio');
    }
}
