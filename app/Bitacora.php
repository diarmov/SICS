<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacoras';

    protected $fillable = [
        'user_id',
        'accion',
        'modulo',
        'detalles',
        'ip_address',
        'user_agent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registrar una acción en la bitácora
     */
    public static function registrar($accion, $modulo, $detalles = null, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return;
        }

        return self::create([
            'user_id' => $user->id,
            'accion' => $accion,
            'modulo' => $modulo,
            'detalles' => $detalles,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
