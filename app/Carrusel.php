<?php
// app/Carrusel.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrusel extends Model
{
    protected $table = 'carrusels';

    protected $fillable = [
        'titulo',
        'imagen',
        'url',
        'orden',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer'
    ];

    // Método auxiliar para obtener un nombre descriptivo para la bitácora
    public function getNombreParaBitacora()
    {
        return $this->titulo . " (Orden: " . $this->orden . ")";
    }

    // Eventos para registrar en bitácora
    protected static function boot()
    {
        parent::boot();

        static::created(function ($carrusel) {
            if (auth()->check()) {
                \App\Bitacora::registrar(
                    'Creación',
                    'Carrusel',
                    "Imagen agregada al carrusel: " . $carrusel->getNombreParaBitacora()
                );
            }
        });

        static::updated(function ($carrusel) {
            if (auth()->check()) {
                // Detectar cambios específicos para información más detallada
                $cambios = [];
                $original = $carrusel->getOriginal();
                $changes = $carrusel->getChanges();

                foreach ($changes as $campo => $nuevoValor) {
                    if ($campo === 'updated_at') continue;

                    $valorAnterior = $original[$campo] ?? 'N/A';

                    // Para campos booleanos, mostrar texto amigable
                    if ($campo === 'activo') {
                        $valorAnteriorTexto = $valorAnterior ? 'Activo' : 'Inactivo';
                        $nuevoValorTexto = $nuevoValor ? 'Activo' : 'Inactivo';
                        $cambios[] = ucfirst($campo) . ": {$valorAnteriorTexto} → {$nuevoValorTexto}";
                    } else {
                        $cambios[] = ucfirst($campo) . ": {$valorAnterior} → {$nuevoValor}";
                    }
                }

                $detalles = "Imagen actualizada: " . $carrusel->getNombreParaBitacora();
                if (!empty($cambios)) {
                    $detalles .= " | Cambios: " . implode(', ', $cambios);
                }

                \App\Bitacora::registrar(
                    'Actualización',
                    'Carrusel',
                    $detalles
                );
            }
        });

        static::deleted(function ($carrusel) {
            if (auth()->check()) {
                \App\Bitacora::registrar(
                    'Eliminación',
                    'Carrusel',
                    "Imagen eliminada del carrusel: " . $carrusel->getNombreParaBitacora()
                );
            }
        });
    }
}
