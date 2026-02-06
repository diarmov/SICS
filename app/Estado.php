<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'clave',
        'activo',
        'usuario_creacion',
        'usuario_modificacion'
    ];

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'id_estado', 'id_estado');
    }

    public function comites()
    {
        return $this->hasMany(ComiteVigilancia::class, 'id_estado', 'id_estado');
    }
}
