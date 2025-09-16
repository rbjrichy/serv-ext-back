<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Estudios de Laboratorio</title>
    <style>
        body {
            font-size: 10pt;
            font-family: Arial, sans-serif;
        }

        .section-title {
            font-weight: bold;
            margin-top: 15px;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
        }

        .field {
            display: table-cell;
            padding: 5px;
            border: 1px solid black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .signature-container {
            text-align: center;
            margin-top: 50px;
        }

        .signature {
            display: inline-block;
            width: 40%;
            text-align: center;
            margin: 20px;
        }

        .signature-line {
            border-top: 1px solid black;
            width: 100%;
        }

        .title-section {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 80px;
        }

        header {
            display: table;
            /* Usar table para simular layout de columnas */
            width: 100%;
            margin-bottom: 10px;
        }

        .header-left,
        .header-center,
        .header-right {
            display: table-cell;
            vertical-align: middle;
        }

        .header-left {
            margin: 0;
            width: 10%;
            text-align: left;
        }

        .header-left .logo {
            width: 50px;
            /* Ajusta el tamaño del logo */
            height: auto;
            float: left;
            /* Para que el texto se envuelva alrededor del logo */
        }



        .header-center {
            width: 60%;
            text-align: center;
        }

        .header-center h1 {
            /* Naranja */
            margin: 0;
            font-size: 14px;
            /* Tamaño de fuente para el título principal */
            display: inline-block;
            /* Para que el padding se aplique correctamente */
            width: 100%;
            box-sizing: border-box;
        }

        .header-right {
            margin: 0;
            width: 20%;
            text-align: left;
        }

        .section-box {
            margin-bottom: 10px;
            border: 1px solid #ccc;
            padding: 5px;
        }



        .white-bg {
            background-color: #FFFFFF;
        }

        .section-title {
            background-color: #D3D3D3;
            /* Gris claro */
            color: black;
            padding: 3px 5px;
            margin: -5px -5px 5px -5px;
            /* Ajusta los márgenes para ocupar el ancho completo del padding del padre */
            font-size: 11px;
            font-weight: bold;
        }

        .section-urgente {
            background-color: #fff;
            /* Gris claro */
            text-align: center;
            color: black;
            padding: 3px 5px;
            margin: -5px -5px 5px -5px;
            /* Ajusta los márgenes para ocupar el ancho completo del padding del padre */
            font-size: 14px;
            font-weight: bold;
        }

        .yellow-bg-title {
            background-color: #FFA500 !important;
            /* Naranja para el título de Fisioterapia Resultados */
            color: white !important;
        }

        .flex-container {
            display: flex;
            flex-wrap: wrap;
            /* Permite que los elementos pasen a la siguiente línea */
            width: 100%;
            box-sizing: border-box;
        }

        .flex-row {
            width: 100%;
            /* Cada .flex-row ocupa el 100% para crear una nueva "fila" */
            display: flex;
            /* Convierte cada fila en un nuevo contenedor flex */
            padding: 2px 0;
            /* Padding vertical para cada fila */
            box-sizing: border-box;
            align-items: baseline;
            /* Alinea los elementos a la línea base de su texto */
        }

        /* Reglas generales para label y value dentro de cualquier flex-row
   Serán sobrescritas si es necesario por reglas más específicas */
        .flex-row .label {
            font-weight: bold;
            margin-right: 5px;
            white-space: nowrap;
            display: inline;
            /* Asegura que sean inline por defecto */
        }

        .flex-row .value {
            word-wrap: break-word;
            display: inline;
            /* Asegura que sean inline por defecto */
        }

        /* ... Estilos para .full-width-item se mantienen ... */


        /* NUEVAS REGLAS PARA COLUMNAS CON FLOAT */

        .column-item {
            float: left;
            /* Hace que el elemento flote a la izquierda */
            width: 100%;
            /* Ocupa la mitad del ancho del padre */
            padding: 0 5px;
            /* Padding horizontal para separación */
            box-sizing: border-box;
        }

        .column-item-2 {
            float: left;
            /* Hace que el elemento flote a la izquierda */
            width: 50%;
            /* Ocupa la mitad del ancho del padre */
            padding: 0 5px;
            /* Padding horizontal para separación */
            box-sizing: border-box;
        }

        .column-item-3 {
            float: left;
            /* Hace que el elemento flote a la izquierda */
            width: 33.33%;
            /* Ocupa la mitad del ancho del padre */
            padding: 0 5px;
            /* Padding horizontal para separación */
            box-sizing: border-box;
        }

        /* Para 3 columnas: */
        /* .column-item.three-cols { width: 33.33%; } */

        .column-item .label,
        .column-item .value {
            display: inline;
            /* Asegura que label y value estén en línea dentro de la columna */
        }

        .column-item-2 .label,
        .column-item-2 .value {
            display: inline;
            /* Asegura que label y value estén en línea dentro de la columna */
        }

        .column-item-3 .label,
        .column-item-3 .value {
            display: inline;
            /* Asegura que label y value estén en línea dentro de la columna */
        }

        .clearfix::after,
        .clear-both {
            content: "";
            display: block;
            clear: both;
            /* Limpia los flotadores */
        }


        /* ---------------------------------------------------- */
        /* FIN NUEVAS REGLAS FLEXBOX */
        /* ---------------------------------------------------- */

        /* Las reglas result-row se mantienen igual si no cambias su estructura a flexbox */
        .result-row {
            margin-bottom: 5px;
            padding: 2px 0;
        }

        .result-row .label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
            /* Ancho fijo para las etiquetas de resultados */
            vertical-align: top;
        }

        .result-row .value {
            display: inline-block;
            width: calc(100% - 105px);
            /* Resta el ancho de la etiqueta + un pequeño margen */
            vertical-align: top;
            word-wrap: break-word;
        }
    </style>
</head>

<body>

    <header>
        <div class="header-left">
            <img src="{{ $logo }}" alt="Logo Universidad" class="logo">
        </div>
        <div class="header-center">
            <h1>INFORME LABORATORIO - SSU</h1>
        </div>
        <div class="header-right">
            <p> <b>conex: </b> {{ $conex }}</p>
            <p> <b>codsol: </b> {{ $solicitud->id }}</p>
        </div>
    </header>

    <section class="section-box yellow-bg">
        <h2 class="section-title">DATOS AFILIADO</h2>
        @if ($solicitud->urgente == 1)
            <h2 class="section-urgente">URGENTE</h2>
        @endif
        <div class="flex-container">
            <div class="flex-row">
                <div class="column-item-2"> <span class="label">Nombres y Apellidos:</span>
                    <span class="value">{{ $afiliado->paterno ?? '' }} {{ $afiliado->materno ?? '' }}
                        {{ $afiliado->nombre }}</span>
                </div>
                <div class="column-item-2"> <span class="label">Matricula:</span>
                    <span class="value">{{ $afiliado->matricula }}</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
        <div class="flex-container">
            <div class="flex-row">
                <div class="column-item-3">
                    <span class="label">Tipo Afiliado:</span>
                    <span class="value">{{ $afiliado->tipo_afiliado }}</span>
                </div>
                <div class="column-item-3">
                    <span class="label">Fecha Nac.:</span>
                    <span class="value">{{ date('d/m/Y', strtotime($afiliado->fecha_nac)) }}</span>
                </div>
                <div class="column-item-3">
                    <span class="label">Código:</span>
                    <span class="value">{{ $afiliado->codigo_referencia }}</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
        <div class="flex-container">
            <div class="flex-row">
                <div class="column-item-3">
                    <span class="label">Institucion:</span>
                    <span class="value">{{ $afiliado->institucion }}</span>
                </div>
                <div class="column-item-3">
                    <span class="label">Fecha Solicitud:</span>
                    <span class="value">{{ date('d/m/Y H:i', strtotime($solicitud->fecha_solicitud)) }}</span>
                </div>
                <div class="column-item-3">
                    <span class="label">Edad:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($afiliado->fecha_nac)->age }}</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
        <div class="flex-container">
            <div class="flex-row">
                <div class="column-item">
                    <span class="label">Especialidad - Médico:</span>
                    <span class="value">{{ $solicitud->especialidad }} -
                        {{ $solicitud->paterno_medico ?? '' }} {{ $solicitud->materno_medico ?? '' }}
                        {{ $solicitud->nombre_medico }}</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
        <div class="flex-container">
            <div class="flex-row">
                <div class="column-item">
                    <span class="label">Diagnóstico:</span>
                    <span class="value">{{ $solicitud->diagnostico }}</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
        <div class="flex-container">
            <div class="flex-row">
                <div class="column-item">
                    <span class="label">Observaciones Médicas:</span>
                    <span class="value">{{ $solicitud->observaciones }}</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
    </section>


    <h5 class="section-title">Lista Examenes</h5>
    <table>
        <thead>
            <tr>
                {{-- <th>CATEGORIA</th> --}}
                <th>SUBCATEGORIA</th>
                <th>EXAMEN</th>
                <th>RESULTADO</th>
                <th>REFERENCIA</th>
                <th>UNIDAD</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($examenes as $item)
                @foreach ($item['subcategorias'] as $index => $subcategoria)
                    @foreach ($subcategoria['examenes'] as $indexExa => $examen)
                        @if ($indexExa === 0)
                            <tr>
                                {{-- <td rowspan="{{ $item['total']}}">{{ $item['categoria'] }}</td> --}}
                                <td rowspan="{{ $subcategoria['total'] }}">{{ $subcategoria['nombre'] }}</td>
                                <td>{{ $examen['nombre_examen'] }}</td>
                                <td>{{ $examen['resultado'] }}</td>
                                <td>{{ $examen['referencia'] }}</td>
                                <td>{{ $examen['unidad'] }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>{{ $examen['nombre_examen'] }}</td>
                                <td>{{ $examen['resultado'] }}</td>
                                <td>{{ $examen['referencia'] }}</td>
                                <td>{{ $examen['unidad'] }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            @endforeach

            {{-- <tr>
                <td>
                    <pre>{{ var_dump($examenes) }}</pre>
                </td>
            </tr> --}}


        </tbody>
    </table>

    <div class="signature-container">
        <div class="signature">
            <div class="signature-line"></div>
            <b>Médico Solicitante</b>
        </div>
        <div class="signature">
            <div class="signature-line"></div>
            <b>Firma</b>
        </div>
    </div>

</body>

</html>
