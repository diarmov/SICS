<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Comités de Vigilancia</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #7c0a02;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #7c0a02;
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .filtros {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #7c0a02;
            font-size: 9px;
        }

        .stats {
            margin-bottom: 20px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .stats-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .stats-table td:first-child {
            background-color: #7c0a02;
            color: white;
            font-weight: bold;
            width: 40%;
        }

        .title-section {
            background-color: #7c0a02;
            color: white;
            padding: 5px;
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #c1c3c5;
            border: 1px solid #999;
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }

        td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 8px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Sistema Informático de Contraloría Social (SICS)</h1>
        <p>Reporte de Comités de Vigilancia</p>
        <p>Generado: {{ $fecha_generacion->format('d/m/Y H:i:s') }} | Usuario: {{ $usuario->nombre_completo }}</p>
    </div>

    <div class="filtros">
        <strong>Filtros aplicados:</strong><br>
        @if(empty($filtros) || (!isset($filtros['dependencia_id']) && !isset($filtros['programa_id']) &&
        !isset($filtros['comite_id']) && !isset($filtros['estado_validacion']) && !isset($filtros['fecha_inicio'])))
        Todos los registros
        @else
        @if(isset($filtros['dependencia_id']) && $filtros['dependencia_id'])
        Dependencia: {{ \App\Dependencia::find($filtros['dependencia_id'])->siglas ?? 'N/A' }}<br>
        @endif
        @if(isset($filtros['programa_id']) && $filtros['programa_id'])
        Programa: {{ \App\Programa::find($filtros['programa_id'])->nombre ?? 'N/A' }}<br>
        @endif
        @if(isset($filtros['comite_id']) && $filtros['comite_id'])
        Comité: {{ \App\ComiteVigilancia::find($filtros['comite_id'])->nombre ?? 'N/A' }}<br>
        @endif
        @if(isset($filtros['estado_validacion']) && $filtros['estado_validacion'] && $filtros['estado_validacion'] !=
        'todos')
        Estado: {{ $filtros['estado_validacion'] == 'validados' ? 'Validados' : 'Pendientes' }}<br>
        @endif
        @if(isset($filtros['fecha_inicio']) && $filtros['fecha_inicio'])
        Fecha desde: {{ $filtros['fecha_inicio'] }}<br>
        @endif
        @if(isset($filtros['fecha_fin']) && $filtros['fecha_fin'])
        Fecha hasta: {{ $filtros['fecha_fin'] }}
        @endif
        @endif
    </div>

    <!-- Resumen General -->
    <div class="title-section">RESUMEN GENERAL</div>
    <table class="stats-table">
        <tr>
            <td>Total de Comités de Vigilancia</td>
            <td class="text-right">{{ number_format($estadisticas['total_comites']) }}</td>
        </tr>
        <tr>
            <td>Comités Validados</td>
            <td class="text-right">{{ number_format($estadisticas['total_validados']) }}</td>
        </tr>
        <tr>
            <td>Comités Pendientes</td>
            <td class="text-right">{{ number_format($estadisticas['total_pendientes']) }}</td>
        </tr>
        <tr>
            <td>Total de Personas Beneficiadas</td>
            <td class="text-right">{{ number_format($estadisticas['total_beneficiarios']) }}</td>
        </tr>
        <tr>
            <td>Total de Monto Vigilado</td>
            <td class="text-right">${{ number_format($estadisticas['total_monto_vigilado'], 2) }}</td>
        </tr>
        <tr>
            <td>Total de Elementos en Comités</td>
            <td class="text-right">{{ number_format($estadisticas['total_elementos']) }}</td>
        </tr>
        <tr>
            <td>Total de Material de Difusión</td>
            <td class="text-right">{{ number_format($estadisticas['total_material_difusion']) }}</td>
        </tr>
        <tr>
            <td>Promedio de Beneficiarios por Comité</td>
            <td class="text-right">{{ number_format($estadisticas['promedio_beneficiarios']) }}</td>
        </tr>
        <tr>
            <td>Promedio de Monto Vigilado por Comité</td>
            <td class="text-right">${{ number_format($estadisticas['promedio_monto'], 2) }}</td>
        </tr>
    </table>

    <!-- Conteo por Tipo de Apoyo -->
    @if(count($porTipoApoyo) > 0)
    <div class="title-section">CONCENTRADO POR TIPO DE APOYO</div>
    <table>
        <thead>
            <tr>
                <th>Tipo de Apoyo</th>
                <th>Comités</th>
                <th>Beneficiarios</th>
                <th>Monto Vigilado</th>
                <th>Elementos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($porTipoApoyo as $item)
            <tr>
                <td>{{ $item['nombre'] }}</td>
                <td class="text-center">{{ $item['total_comites'] }}</td>
                <td class="text-right">{{ number_format($item['total_beneficiarios']) }}</td>
                <td class="text-right">${{ number_format($item['total_monto_vigilado'], 2) }}</td>
                <td class="text-center">{{ $item['total_elementos'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Materiales de Difusión por Comité -->
    <div class="title-section">MATERIAL DE DIFUSIÓN POR COMITÉ</div>
    <table>
        <thead>
            <tr>
                <th>Comité</th>
                <th>Dependencia</th>
                <th>Programa</th>
                <th>Validado</th>
                <th>Total Materiales</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materialesPorComite as $item)
            <tr>
                <td>{{ $item['comite_nombre'] }}</td>
                <td>{{ $item['dependencia'] }}</td>
                <td>{{ Str::limit($item['programa'], 30) }}</td>
                <td class="text-center">{{ $item['validado'] ? '✓' : '✗' }}</td>
                <td class="text-center">{{ number_format($item['total_materiales']) }}</td>
                <td>
                    @foreach($item['detalle_materiales'] as $tipo => $cantidad)
                    {{ $tipo }}: {{ $cantidad }}@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Listado Detallado de Comités -->
    <div class="title-section page-break">LISTADO DETALLADO DE COMITÉS</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre del Comité</th>
                <th>Dependencia</th>
                <th>Programa</th>
                <th>Ubicación</th>
                <th>Elementos</th>
                <th>Beneficiarios</th>
                <th>Monto Vigilado</th>
                <th>Validado</th>
                <th>Materiales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comites as $index => $comite)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $comite->nombre }}</td>
                <td>{{ $comite->dependencia->siglas ?? 'N/A' }}</td>
                <td>{{ Str::limit($comite->programa->nombre ?? 'N/A', 25) }}</td>
                <td>{{ $comite->ubicacion_completa }}</td>
                <td class="text-center">{{ $comite->elementos->count() }}</td>
                <td class="text-right">{{ number_format($comite->programa->numero_beneficiarios ?? 0) }}</td>
                <td class="text-right">${{ number_format($comite->programa->monto_vigilado ?? 0, 2) }}</td>
                <td class="text-center">{{ $comite->estaValidado() ? '✓' : '✗' }}</td>
                <td class="text-center">
                    @php
                    $total = 0;
                    foreach($comite->material_difusion as $m) { $total += $m['cantidad'] ?? 1; }
                    @endphp
                    {{ number_format($total) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        SICS - Sistema Informático de Contraloría Social | Página {PAGE_NUM} de {PAGE_COUNT}
    </div>
</body>

</html>