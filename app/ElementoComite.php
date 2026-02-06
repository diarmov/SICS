<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ElementoComite extends Model
{
    protected $table = 'elementos_comite'; // Especificar el nombre exacto de la tabla

    protected $fillable = [
        'comite_vigilancia_id',
        'nombre_completo',
        'tipo_elemento',
        'archivo_ine'
    ];

    public function comiteVigilancia()
    {
        return $this->belongsTo(ComiteVigilancia::class, 'comite_vigilancia_id');
    }
    /**
     * Obtener nombre para bitácora
     */
    public function getNombreParaBitacora()
    {
        return $this->nombre_completo . " (" . $this->tipo_elemento . ") - Comité ID: " . $this->comite_vigilancia_id;
    }

    /**
     * Obtener la URL del archivo INE
     */
    public function getArchivoIneUrlAttribute()
    {
        if ($this->archivo_ine && Storage::disk('public')->exists($this->archivo_ine)) {
            return Storage::disk('public')->url($this->archivo_ine);
        }
        return null;
    }

    /**
     * Eliminar archivo INE al eliminar el elemento
     */
    public function deleteArchivoIne()
    {
        if ($this->archivo_ine && Storage::disk('public')->exists($this->archivo_ine)) {
            Storage::disk('public')->delete($this->archivo_ine);
        }
    }


    /**
     * Boot method para registrar eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($elemento) {
            if (auth()->check()) {
                Bitacora::registrar('Creación', 'Elementos de Comité', "Elemento agregado: " . $elemento->getNombreParaBitacora());
            }
        });

        static::updated(function ($elemento) {
            if (auth()->check()) {
                Bitacora::registrar('Actualización', 'Elementos de Comité', "Elemento actualizado: " . $elemento->getNombreParaBitacora());
            }
        });

        static::deleting(function ($elemento) {
            // Eliminar archivo físico al eliminar el registro
            $elemento->deleteArchivoIne();
        });

        static::deleted(function ($elemento) {
            if (auth()->check()) {
                Bitacora::registrar('Eliminación', 'Elementos de Comité', "Elemento eliminado: " . $elemento->getNombreParaBitacora());
            }
        });
    }
}
