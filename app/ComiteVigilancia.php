<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ComiteVigilancia extends Model
{
    protected $table = 'comites_vigilancia'; // Especificar el nombre exacto de la tabla

    protected $fillable = [
        'dependencia_id',
        'programa_id',
        'nombre',
        'id_estado',
        'id_municipio',
        'id_localidad',
        'activo',
        'archivo_minuta'
    ];

    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class);
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function elementos()
    {
        return $this->hasMany(ElementoComite::class);
    }
    /**
     * Obtener nombre para bitácora
     */
    public function getNombreParaBitacora()
    {
        return $this->nombre . " (" . $this->dependencia->siglas . ")";
    }

    // Nuevas relaciones

    public function estado()
    {
        return $this->belongsTo(\App\Estado::class, 'id_estado', 'id_estado');
    }

    public function municipio()
    {
        return $this->belongsTo(\App\Municipio::class, 'id_municipio', 'id_municipio');
    }

    public function localidad()
    {
        return $this->belongsTo(\App\Localidad::class, 'id_localidad', 'id_localidad');
    }

    /**
     * Obtener la URL del archivo de minuta
     */
    public function getMinutaUrlAttribute()
    {
        if ($this->archivo_minuta && Storage::disk('public')->exists($this->archivo_minuta)) {
            return Storage::disk('public')->url($this->archivo_minuta);
        }
        return null;
    }

    /**
     * Eliminar archivo de minuta al eliminar el comité
     */
    public function deleteMinuta()
    {
        if ($this->archivo_minuta && Storage::disk('public')->exists($this->archivo_minuta)) {
            Storage::disk('public')->delete($this->archivo_minuta);
        }
    }


    /**
     * Boot method para registrar eventos del modelo
     */
    // Método para obtener ubicación completa
    public function getUbicacionCompletaAttribute()
    {
        $ubicacion = [];

        if ($this->localidad) {
            $ubicacion[] = $this->localidad->nombre;
        }

        if ($this->municipio) {
            $ubicacion[] = $this->municipio->nombre;
        }

        if ($this->estado) {
            $ubicacion[] = $this->estado->nombre;
        }

        return implode(', ', array_reverse($ubicacion));
    }

    /**
     * Boot method para registrar eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($comite) {
            if (auth()->check()) {
                Bitacora::registrar('Creación', 'Comités de Vigilancia', "Comité creado: " . $comite->getNombreParaBitacora());
            }
        });

        static::updated(function ($comite) {
            if (auth()->check()) {
                Bitacora::registrar('Actualización', 'Comités de Vigilancia', "Comité actualizado: " . $comite->getNombreParaBitacora());
            }
        });

        static::deleting(function ($comite) {
            // Eliminar archivo de minuta al eliminar el comité
            $comite->deleteMinuta();
        });

        static::deleted(function ($comite) {
            if (auth()->check()) {
                Bitacora::registrar('Eliminación', 'Comités de Vigilancia', "Comité eliminado: " . $comite->getNombreParaBitacora());
            }
        });
    }
}
