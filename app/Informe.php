<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
    protected $table = 'informes';

    protected $fillable = [
        'programa_id',
        'numero_informe',
        'nombre',
        'archivo',
        'observaciones',
        'fecha_entrega',
        'entregado'
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'entregado' => 'boolean'
    ];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    // Método para obtener el nombre del archivo
    public function getNombreArchivoAttribute()
    {
        if ($this->archivo) {
            return basename($this->archivo);
        }
        return null;
    }

    // Boot method para registrar en bitácora
    protected static function boot()
    {
        parent::boot();

        static::created(function ($informe) {
            if (auth()->check()) {
                \App\Bitacora::registrar(
                    'Creación',
                    'Informes',
                    "Informe {$informe->numero_informe} creado para programa: {$informe->programa->nombre}"
                );
            }
        });

        static::updated(function ($informe) {
            if (auth()->check()) {
                \App\Bitacora::registrar(
                    'Actualización',
                    'Informes',
                    "Informe {$informe->numero_informe} actualizado para programa: {$informe->programa->nombre}"
                );
            }
        });

        static::deleted(function ($informe) {
            if (auth()->check()) {
                \App\Bitacora::registrar(
                    'Eliminación',
                    'Informes',
                    "Informe {$informe->numero_informe} eliminado para programa: {$informe->programa->nombre}"
                );
            }
        });
    }
}
