<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoApoyo extends Model
{
    protected $table = 'tipos_apoyo';

    protected $fillable = [
        'nombre',
        'fecha_alta',
        'activo'
    ];

    protected $casts = [
        'fecha_alta' => 'date',
        'activo' => 'boolean'
    ];

    // Relación con programas
    public function programas()
    {
        return $this->hasMany(\App\Programa::class, 'tipo_apoyo_id');
    }
}
