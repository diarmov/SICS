<?php

namespace App\Traits;

use App\Bitacora;

trait RegistraBitacora
{
    /**
     * Registrar acción en bitácora
     */
    protected function registrarBitacora($accion, $modulo, $detalles = null)
    {
        Bitacora::registrar($accion, $modulo, $detalles);
    }

    /**
     * Registrar creación en bitácora
     */
    protected function registrarCreacion($modelo, $modulo)
    {
        $detalles = "Registro creado: " . $this->obtenerDetallesModelo($modelo);
        $this->registrarBitacora('Creación', $modulo, $detalles);
    }

    /**
     * Registrar actualización en bitácora
     */
    protected function registrarActualizacion($modelo, $modulo)
    {
        $detalles = "Registro actualizado: " . $this->obtenerDetallesModelo($modelo);
        $this->registrarBitacora('Actualización', $modulo, $detalles);
    }

    /**
     * Registrar eliminación en bitácora
     */
    protected function registrarEliminacion($modelo, $modulo)
    {
        $detalles = "Registro eliminado: " . $this->obtenerDetallesModelo($modelo);
        $this->registrarBitacora('Eliminación', $modulo, $detalles);
    }

    /**
     * Obtener detalles del modelo para la bitácora
     */
    protected function obtenerDetallesModelo($modelo)
    {
        if (method_exists($modelo, 'getNombreParaBitacora')) {
            return $modelo->getNombreParaBitacora();
        }

        if (isset($modelo->nombre)) {
            return $modelo->nombre;
        }

        if (isset($modelo->email)) {
            return $modelo->email;
        }

        if (isset($modelo->dependencia)) {
            return $modelo->dependencia;
        }

        return "ID: " . $modelo->id;
    }
}
