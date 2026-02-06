<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    protected $fillable = [
        'dependencia',
        'siglas',
        'activo'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function programas()
    {
        return $this->hasMany(Programa::class);
    }

    public function comitesVigilancia()
    {
        return $this->hasMany(ComiteVigilancia::class);
    }
    /**
     * Obtener nombre para bitácora
     */
    public function getNombreParaBitacora()
    {
        return $this->dependencia . " (" . $this->siglas . ")";
    }

    /**
     * Boot method para registrar eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($dependencia) {
            if (auth()->check()) {
                Bitacora::registrar('Creación', 'Dependencias', "Dependencia creada: " . $dependencia->getNombreParaBitacora());
            }
        });

        static::updated(function ($dependencia) {
            if (auth()->check()) {
                Bitacora::registrar('Actualización', 'Dependencias', "Dependencia actualizada: " . $dependencia->getNombreParaBitacora());
            }
        });

        static::deleted(function ($dependencia) {
            if (auth()->check()) {
                Bitacora::registrar('Eliminación', 'Dependencias', "Dependencia eliminada: " . $dependencia->getNombreParaBitacora());
            }
        });
    }
}
