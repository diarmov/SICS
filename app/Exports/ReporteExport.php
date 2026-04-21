<?php
// app/Exports/ReporteExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Datos a exportar
     */
    public function array(): array
    {
        $rows = [];

        // Título del reporte
        $rows[] = ['SISTEMA INFORMÁTICO DE CONTRALORÍA SOCIAL (SICS)'];
        $rows[] = ['REPORTE DE COMITÉS DE VIGILANCIA'];
        $rows[] = ['Fecha de generación: ' . $this->data['fecha_generacion']->format('d/m/Y H:i:s')];
        $rows[] = ['Usuario: ' . $this->data['usuario']->nombre_completo];
        $rows[] = [];

        // Filtros aplicados
        $rows[] = ['FILTROS APLICADOS:'];
        $filtrosTexto = $this->getFiltrosTexto();
        $rows[] = [$filtrosTexto];
        $rows[] = [];

        // ========== RESUMEN GENERAL ==========
        $rows[] = ['RESUMEN GENERAL'];
        $stats = $this->data['estadisticas'];
        $rows[] = ['Total de Comités de Vigilancia', number_format($stats['total_comites'])];
        $rows[] = ['Comités Validados', number_format($stats['total_validados'])];
        $rows[] = ['Comités Pendientes', number_format($stats['total_pendientes'])];
        $rows[] = ['Total de Personas Beneficiadas', number_format($stats['total_beneficiarios'])];
        $rows[] = ['Total de Monto Vigilado', '$' . number_format($stats['total_monto_vigilado'], 2)];
        $rows[] = ['Total de Elementos en Comités', number_format($stats['total_elementos'])];
        $rows[] = ['Total de Material de Difusión', number_format($stats['total_material_difusion'])];
        $rows[] = ['Promedio de Beneficiarios por Comité', number_format($stats['promedio_beneficiarios'])];
        $rows[] = ['Promedio de Monto Vigilado por Comité', '$' . number_format($stats['promedio_monto'], 2)];
        $rows[] = [];

        // ========== CONCENTRADO POR TIPO DE APOYO ==========
        if (count($this->data['porTipoApoyo']) > 0) {
            $rows[] = ['CONCENTRADO POR TIPO DE APOYO'];
            $rows[] = ['Tipo de Apoyo', 'Comités', 'Beneficiarios', 'Monto Vigilado', 'Elementos'];
            foreach ($this->data['porTipoApoyo'] as $item) {
                $rows[] = [
                    $item['nombre'],
                    $item['total_comites'],
                    number_format($item['total_beneficiarios']),
                    '$' . number_format($item['total_monto_vigilado'], 2),
                    $item['total_elementos']
                ];
            }
            $rows[] = [];
        }

        // ========== MATERIALES DE DIFUSIÓN POR COMITÉ ==========
        $rows[] = ['MATERIAL DE DIFUSIÓN POR COMITÉ'];
        $rows[] = ['Comité', 'Dependencia', 'Programa', 'Validado', 'Total Materiales', 'Detalle'];
        foreach ($this->data['materialesPorComite'] as $item) {
            $detalle = '';
            foreach ($item['detalle_materiales'] as $tipo => $cantidad) {
                $detalle .= $tipo . ': ' . $cantidad . '; ';
            }
            $detalle = rtrim($detalle, '; ');
            $rows[] = [
                $item['comite_nombre'],
                $item['dependencia'],
                $this->truncateText($item['programa'], 50),
                $item['validado'] ? 'Sí' : 'No',
                $item['total_materiales'],
                $detalle ?: 'Sin materiales'
            ];
        }
        $rows[] = [];

        // ========== LISTADO DETALLADO DE COMITÉS ==========
        $rows[] = ['LISTADO DETALLADO DE COMITÉS'];
        $rows[] = ['#', 'Nombre del Comité', 'Dependencia', 'Programa', 'Ubicación', 'Elementos', 'Beneficiarios', 'Monto Vigilado', 'Validado', 'Materiales', 'Fecha Creación'];

        foreach ($this->data['comites'] as $index => $comite) {
            $totalMateriales = 0;
            $materiales = $comite->material_difusion;
            if (is_array($materiales)) {
                foreach ($materiales as $m) {
                    $totalMateriales += $m['cantidad'] ?? 1;
                }
            }

            $rows[] = [
                $index + 1,
                $comite->nombre,
                $comite->dependencia->siglas ?? 'N/A',
                $this->truncateText($comite->programa->nombre ?? 'N/A', 40),
                $comite->ubicacion_completa,
                $comite->elementos->count(),
                number_format($comite->programa->numero_beneficiarios ?? 0),
                '$' . number_format($comite->programa->monto_vigilado ?? 0, 2),
                $comite->estaValidado() ? 'Sí' : 'No',
                $totalMateriales,
                $comite->created_at ? $comite->created_at->format('d/m/Y') : 'N/A'
            ];
        }

        return $rows;
    }

    /**
     * Encabezados (no se usan porque los definimos manualmente)
     */
    public function headings(): array
    {
        return [];
    }

    /**
     * Trunca texto si es muy largo
     */
    private function truncateText($text, $length = 50)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }

    /**
     * Obtiene texto descriptivo de los filtros
     */
    private function getFiltrosTexto()
    {
        $filtros = $this->data['filtros'];
        $textos = [];

        if (isset($filtros['dependencia_id']) && $filtros['dependencia_id']) {
            $dependencia = \App\Dependencia::find($filtros['dependencia_id']);
            $textos[] = 'Dependencia: ' . ($dependencia ? $dependencia->siglas : $filtros['dependencia_id']);
        }

        if (isset($filtros['programa_id']) && $filtros['programa_id']) {
            $programa = \App\Programa::find($filtros['programa_id']);
            $textos[] = 'Programa: ' . ($programa ? $programa->nombre : $filtros['programa_id']);
        }

        if (isset($filtros['comite_id']) && $filtros['comite_id']) {
            $comite = \App\ComiteVigilancia::find($filtros['comite_id']);
            $textos[] = 'Comité: ' . ($comite ? $comite->nombre : $filtros['comite_id']);
        }

        if (isset($filtros['estado_validacion']) && $filtros['estado_validacion'] && $filtros['estado_validacion'] != 'todos') {
            $textos[] = 'Estado: ' . ($filtros['estado_validacion'] == 'validados' ? 'Validados' : 'Pendientes');
        }

        if (isset($filtros['fecha_inicio']) && $filtros['fecha_inicio']) {
            $textos[] = 'Desde: ' . $filtros['fecha_inicio'];
        }

        if (isset($filtros['fecha_fin']) && $filtros['fecha_fin']) {
            $textos[] = 'Hasta: ' . $filtros['fecha_fin'];
        }

        return empty($textos) ? 'Todos los registros' : implode(' | ', $textos);
    }
}
