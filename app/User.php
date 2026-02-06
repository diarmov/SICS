<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'password',
        'dependencia_id',
        'activo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class);
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class);
    }

    // Método para obtener el nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno;
    }

    // Método para verificar si el usuario tiene acceso a módulos específicos
    public function puedeAccederAModulo($modulo)
    {
        if ($this->hasRole(['SuperUsuario', 'AdministradorCS'])) {
            return true;
        }

        switch ($modulo) {
            case 'usuarios':
                return $this->hasRole('CoordinadorEnlaces');
            case 'programas':
                return $this->hasRole(['CoordinadorEnlaces', 'EnlacePrograma']);
            case 'comites':
                return $this->hasRole(['CoordinadorEnlaces', 'EnlacePrograma']);
            default:
                return false;
        }
    }

    /**
     * Obtener nombre para bitácora
     */
    public function getNombreParaBitacora()
    {
        return $this->nombre_completo . " (" . $this->email . ")";
    }

    /**
     * Boot method para registrar eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Registrar en bitácora cuando se crea un usuario
        static::created(function ($user) {
            if (auth()->check()) {
                Bitacora::registrar('Creación', 'Usuarios', "Usuario creado: " . $user->getNombreParaBitacora());
            }
        });

        // Registrar en bitácora cuando se actualiza un usuario
        static::updated(function ($user) {
            if (auth()->check()) {
                Bitacora::registrar('Actualización', 'Usuarios', "Usuario actualizado: " . $user->getNombreParaBitacora());
            }
        });

        // Registrar en bitácora cuando se elimina un usuario
        static::deleted(function ($user) {
            if (auth()->check()) {
                Bitacora::registrar('Eliminación', 'Usuarios', "Usuario eliminado: " . $user->getNombreParaBitacora());
            }
        });
    }
}
