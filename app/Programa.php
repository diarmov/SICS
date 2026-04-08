<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Programa extends Model
{
    protected $table = 'programas';

    protected $fillable = [
        'dependencia_id',
        'tipo_apoyo_id',
        'nombre',
        'archivo_pdf',
        'reglas_operacion_pdf',
        'guia_operativa_pdf',
        'fecha_inicio',
        'fecha_termino',
        'periodo',
        'numero_informes',
        'numero_beneficiarios',
        'monto_vigilado',
        'activo',
        'guia_operativa_validada',
        'guia_operativa_observaciones',
        'guia_operativa_validada_por',
        'guia_operativa_fecha_validacion'
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_termino',
        'guia_operativa_fecha_validacion',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
        'numero_beneficiarios' => 'integer',
        'monto_vigilado' => 'decimal:2',
        'activo' => 'boolean',
        'guia_operativa_validada' => 'boolean'
    ];

    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class);
    }

    public function tipoApoyo()
    {
        return $this->belongsTo(\App\TipoApoyo::class, 'tipo_apoyo_id');
    }

    public function comitesVigilancia()
    {
        return $this->hasMany(ComiteVigilancia::class);
    }

    public function informes()
    {
        return $this->hasMany(Informe::class);
    }

    public function validador()
    {
        return $this->belongsTo(User::class, 'guia_operativa_validada_por');
    }

    // Método para verificar si el programa está activo (dentro del periodo Y validado)
    public function getEstaActivoAttribute()
    {
        $hoy = Carbon::now();
        $enPeriodo = $hoy->between($this->fecha_inicio, $this->fecha_termino);

        // Solo está activo si está en periodo Y tiene la guía operativa validada
        return $enPeriodo && $this->guia_operativa_validada && $this->activo;
    }

    // Método para verificar si la guía operativa está pendiente de validación
    public function getGuiaOperativaPendienteAttribute()
    {
        return $this->guia_operativa_pdf && !$this->guia_operativa_validada;
    }

    // Método para verificar si se pueden agregar informes
    public function getPuedeAgregarInformesAttribute()
    {
        return $this->esta_activo && $this->informes->count() < $this->numero_informes;
    }

    // Método para obtener informes pendientes
    public function getInformesPendientesAttribute()
    {
        return $this->numero_informes - $this->informes->count();
    }

    public function getNombreParaBitacora()
    {
        return $this->nombre . " (" . $this->dependencia->siglas . " - " . $this->periodo . ")";
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($programa) {
            if (auth()->check()) {
                \App\Bitacora::registrar('Creación', 'Programas', "Programa creado: " . $programa->getNombreParaBitacora());
            }
        });

        static::updated(function ($programa) {
            if (auth()->check()) {
                \App\Bitacora::registrar('Actualización', 'Programas', "Programa actualizado: " . $programa->getNombreParaBitacora());
            }
        });

        static::deleted(function ($programa) {
            if (auth()->check()) {
                \App\Bitacora::registrar('Eliminación', 'Programas', "Programa eliminado: " . $programa->getNombreParaBitacora());
            }
        });
    }
}
