<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ComiteVigilancia extends Model
{
    protected $table = 'comites_vigilancia';

    protected $fillable = [
        'dependencia_id',
        'programa_id',
        'nombre',
        'id_estado',
        'id_municipio',
        'id_localidad',
        'activo',
        'archivo_minuta',
        'lista_asistencia',
        'material_difusion',
        'fotografias_reunion',
        'validado',
        'validado_por',
        'fecha_validacion'
    ];

    /**
     * Atributos que deben ser convertidos a fechas (Carbon instances)
     * Importante para Laravel 7
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'fecha_validacion'
    ];

    // ===== RELACIONES =====

    /**
     * Relación con la dependencia
     * Un comité pertenece a una dependencia
     */
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class);
    }

    /**
     * Relación con el programa
     * Un comité pertenece a un programa
     */
    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    /**
     * Relación con los elementos del comité
     * Un comité tiene muchos elementos (presidentes, vocales, etc.)
     */
    public function elementos()
    {
        return $this->hasMany(ElementoComite::class);
    }

    /**
     * Relación con el estado (ubicación)
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación con el municipio (ubicación)
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }

    /**
     * Relación con la localidad (ubicación)
     */
    public function localidad()
    {
        return $this->belongsTo(Localidad::class, 'id_localidad', 'id_localidad');
    }

    /**
     * Relación con el usuario que validó el comité
     */
    public function validador()
    {
        return $this->belongsTo(User::class, 'validado_por');
    }

    // ===== MÉTODOS DE VALIDACIÓN =====

    /**
     * Verifica si el comité está validado
     *
     * Para que un comité esté validado debe cumplir:
     * 1. El campo validado debe ser true (o 1 en BD)
     * 2. Debe tener un usuario que lo validó (validado_por no nulo)
     * 3. Debe tener una fecha de validación (fecha_validacion no nula)
     *
     * @return bool
     */
    public function estaValidado()
    {
        // En MySQL, true/false se guarda como 1/0, por eso verificamos ambos casos
        $validado = $this->validado == 1 || $this->validado === true;

        return $validado &&
            !is_null($this->validado_por) &&
            !is_null($this->fecha_validacion);
    }

    /**
     * Valida el comité
     * Establece los campos de validación y guarda en BD
     *
     * @param int $usuarioId ID del usuario que valida
     * @return bool
     */
    public function validar($usuarioId)
    {
        $this->validado = true;      // En BD se guardará como 1
        $this->validado_por = $usuarioId;
        $this->fecha_validacion = now();

        // Usamos save() en lugar de update() para asegurar que los cambios
        // se apliquen inmediatamente en la instancia actual
        return $this->save();
    }

    /**
     * Invalida el comité
     * Elimina los campos de validación y guarda en BD
     *
     * @return bool
     */
    public function invalidar()
    {
        $this->validado = false;      // En BD se guardará como 0
        $this->validado_por = null;
        $this->fecha_validacion = null;

        // Usamos save() para asegurar que la instancia actual refleje los cambios
        return $this->save();
    }

    // ===== SCOPES =====

    /**
     * Scope para filtrar comités validados
     */
    public function scopeValidados($query)
    {
        return $query->where('validado', true);
    }

    /**
     * Scope para filtrar comités pendientes de validación
     * Incluye comités con validado = false O null
     */
    public function scopePendientes($query)
    {
        return $query->where(function ($q) {
            $q->where('validado', false)->orWhereNull('validado');
        });
    }

    // ===== ACCESSORS Y MUTATORS =====

    /**
     * Obtiene el nombre completo para mostrar en bitácora
     *
     * @return string
     */
    public function getNombreParaBitacora()
    {
        $siglas = optional($this->dependencia)->siglas ?? 'Sin dependencia';
        return $this->nombre . " (" . $siglas . ")";
    }

    /**
     * Obtiene la ubicación completa del comité
     * Formato: Localidad, Municipio, Estado
     *
     * @return string
     */
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
     * Accessor para obtener la URL de la minuta
     *
     * @return string|null
     */
    public function getMinutaUrlAttribute()
    {
        if ($this->archivo_minuta && Storage::disk('public')->exists($this->archivo_minuta)) {
            return Storage::disk('public')->url($this->archivo_minuta);
        }
        return null;
    }

    /**
     * Accessor para obtener la URL de la lista de asistencia
     *
     * @return string|null
     */
    public function getListaAsistenciaUrlAttribute()
    {
        if ($this->lista_asistencia && Storage::disk('public')->exists($this->lista_asistencia)) {
            return Storage::disk('public')->url($this->lista_asistencia);
        }
        return null;
    }

    /**
     * Accessor para decodificar el JSON de material de difusión
     * Convierte el JSON almacenado en un array
     *
     * @param mixed $value Valor del JSON en BD
     * @return array
     */
    public function getMaterialDifusionAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Obtiene las URLs del material de difusión
     *
     * @return array
     */
    public function getMaterialDifusionUrlsAttribute()
    {
        $urls = [];
        foreach ($this->material_difusion as $ruta) {
            if (Storage::disk('public')->exists($ruta)) {
                $urls[] = Storage::disk('public')->url($ruta);
            }
        }
        return $urls;
    }

    /**
     * Accessor para decodificar el JSON de fotografías
     * Convierte el JSON almacenado en un array
     *
     * @param mixed $value Valor del JSON en BD
     * @return array
     */
    public function getFotografiasReunionAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Obtiene las URLs de las fotografías
     *
     * @return array
     */
    public function getFotografiasReunionUrlsAttribute()
    {
        $urls = [];
        foreach ($this->fotografias_reunion as $ruta) {
            if (Storage::disk('public')->exists($ruta)) {
                $urls[] = Storage::disk('public')->url($ruta);
            }
        }
        return $urls;
    }

    /**
     * Elimina el archivo de minuta del almacenamiento
     */
    public function deleteMinuta()
    {
        if ($this->archivo_minuta && Storage::disk('public')->exists($this->archivo_minuta)) {
            Storage::disk('public')->delete($this->archivo_minuta);
        }
    }

    // ===== BOOT METHOD =====

    /**
     * Eventos del modelo para registrar en bitácora
     */
    protected static function boot()
    {
        parent::boot();

        // Al crear un comité
        static::created(function ($comite) {
            if (auth()->check()) {
                Bitacora::registrar(
                    'Creación',
                    'Comités de Vigilancia',
                    "Comité creado: " . $comite->getNombreParaBitacora()
                );
            }
        });

        // Al actualizar un comité
        static::updated(function ($comite) {
            if (auth()->check()) {
                Bitacora::registrar(
                    'Actualización',
                    'Comités de Vigilancia',
                    "Comité actualizado: " . $comite->getNombreParaBitacora()
                );
            }
        });

        // Antes de eliminar un comité (eliminar archivos asociados)
        static::deleting(function ($comite) {
            $comite->deleteMinuta();
            // Aquí podrías eliminar también los otros archivos si es necesario
        });

        // Después de eliminar un comité
        static::deleted(function ($comite) {
            if (auth()->check()) {
                Bitacora::registrar(
                    'Eliminación',
                    'Comités de Vigilancia',
                    "Comité eliminado: " . $comite->getNombreParaBitacora()
                );
            }
        });
    }
}
