<?php

namespace App\Http\Controllers;

use App\Common\ErrorHandler;
use App\Models\Afiliado;
use App\Models\ConexLaboratorioExamen;
use App\Models\LaboratorioSolicitud;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReporteLaboratorioController extends Controller
{
    public function generar_informe($id)
    {
        try {
            $examenes = [];
            $con = 0;
            $path = public_path('favicon.png'); // Ruta de la imagen en la carpeta public
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents($path));

            $solicitud = LaboratorioSolicitud::datos_solicitud($id);
            if (!$solicitud) {
                throw new HttpException(404, 'Solicitud no encontrada');
            }
            $afiliado = Afiliado::datos_paciente($solicitud->paciente_id);
            if (!$afiliado) {
                throw new HttpException(404, 'Solicitud no encontrada');
            }
            $registros = LaboratorioSolicitud::get_examenes_resultados($solicitud->id);
            $conex = null;
            if (!$registros) {
                throw new HttpException(404, 'Solicitud no encontrada');
            }
            while ($registros->isNotEmpty()) {
                $registro = $registros->first();
                $categoria_id = $registro->categoria_id;

                $categoriasExamen = $registros->filter(function ($item) use ($categoria_id) {
                    return $item->categoria_id == $categoria_id;
                });

                $examenes[$con]['categoria'] =  $registro->categoria;
                $examenes[$con]['total'] =  0;
                $con_subcategoria = 0;
                while ($categoriasExamen->isNotEmpty()) {
                    $examenes_subcategorias = [];
                    $subcategoria = $categoriasExamen->first();
                    $subcategoria_id = $subcategoria->subcategoria_id;

                    $subcategoriasExamen = $categoriasExamen->filter(function ($item) use ($subcategoria_id) {
                        return $item->subcategoria_id == $subcategoria_id;
                    });
                    foreach ($subcategoriasExamen as $exa_paciente) {
                        array_push($examenes_subcategorias, [
                            'nombre_examen' => $exa_paciente->nombre_examen,
                            'resultado' => $exa_paciente->resultado,
                            'referencia' => $exa_paciente->referencia,
                            'unidad' => $exa_paciente->unidad,
                        ]);
                    }
                    $examenes[$con]['subcategorias'][$con_subcategoria]['nombre'] = $exa_paciente->subcategoria;
                    $examenes[$con]['subcategorias'][$con_subcategoria]['examenes'] = $examenes_subcategorias;
                    $examenes[$con]['subcategorias'][$con_subcategoria]['total'] = $subcategoriasExamen->count();
                    $categoriasExamen = $categoriasExamen->reject(function ($item) use ($subcategoria_id) {
                        return $item->subcategoria_id == $subcategoria_id;
                    })->values();
                    $examenes[$con]['total'] = $examenes[$con]['total'] + $examenes[$con]['subcategorias'][$con_subcategoria]['total'];
                    $con_subcategoria++;
                }

                $registros = $registros->reject(function ($item) use ($categoria_id) {
                    return $item->categoria_id == $categoria_id;
                })->values();
                $con++;
            }
            $pdf = Pdf::loadView('informe_laboratorio', compact('solicitud', 'afiliado', 'examenes', 'logo', 'conex'));
            return $pdf->stream('informe_laboratorio.pdf');
        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function generar_solicitud($id)
    {
        try {
            $examenes = [];
            $con = 0;
            $path = public_path('favicon.png'); // Ruta de la imagen en la carpeta public
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents($path));

            $solicitud = LaboratorioSolicitud::datos_solicitud($id);
            if (!$solicitud) {
                throw new HttpException(404, 'Solicitud no encontrada');
            }
            $afiliado = Afiliado::datos_paciente($solicitud->paciente_id);
            if (!$afiliado) {
                throw new HttpException(404, 'Solicitud no encontrada');
            }
            $registros = LaboratorioSolicitud::get_examenes_resultados($solicitud->id);
            $conex = null;
            if (!$registros) {
                throw new HttpException(404, 'Solicitud no encontrada');
            }
            while ($registros->isNotEmpty()) {
                $registro = $registros->first();
                $categoria_id = $registro->categoria_id;

                $examenesPaciente = $registros->filter(function ($item) use ($categoria_id) {
                    return $item->categoria_id == $categoria_id;
                });

                $examenes[$con] = [
                    ...(array) $registro,
                ];
                $subcategorias = [];
                foreach ($examenesPaciente as $exa_paciente) {
                    if (!in_array($exa_paciente->subcategoria, $subcategorias)) {
                        array_push($subcategorias, $exa_paciente->subcategoria);
                    }
                }
                $examenes[$con]['subcategorias'] = $subcategorias;

                $registros = $registros->reject(function ($item) use ($categoria_id) {
                    return $item->categoria_id == $categoria_id;
                })->values();
                $con++;
            }
            $pdf = Pdf::loadView('solicitud_laboratorio', compact('solicitud', 'afiliado', 'examenes', 'logo', 'conex'));
            return $pdf->stream('solicitud_laboratorio.pdf');
        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);
        }
    }

    // public function generar_reporte_general_sesiones(ReporteRequest $request)
    // {
    //     try {
    //         $path = public_path('favicon.png'); // Ruta de la imagen en la carpeta public
    //         $logo = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    //         $validado = $request->validated();
    //         $fecha_inicio = Carbon::parse($validado['fecha_inicio']);
    //         $fecha_fin = Carbon::parse($validado['fecha_fin']);

    //         $registros = ReporteFisioterapia::reporte_general_sesiones($validado);
    //         if ($registros->count() == 0) {
    //             throw new HttpException(404, 'No se encontraron registros');
    //         }

    //         $solicitudes = [];
    //         $con = 0;
    //         $totales = 0;
    //         while ($registros->isNotEmpty()) {
    //             $registro = $registros->first();
    //             $solicitud_id = $registro->fisioterapia_solicitud_id;

    //             $solicitudesPaciente = $registros->filter(function ($item) use ($solicitud_id) {
    //                 return $item->fisioterapia_solicitud_id == $solicitud_id;
    //             });

    //             $solicitudes[$con] = [
    //                 ...(array) $registro,
    //             ];
    //             $fechas_sesiones = [];
    //             foreach ($solicitudesPaciente as $sol_paciente) {
    //                 array_push($fechas_sesiones, $sol_paciente->fecha_hora_sesion);
    //             }
    //             $solicitudes[$con]['fechas_sesiones'] = $fechas_sesiones;
    //             $solicitudes[$con]['total'] = $solicitudesPaciente->count();

    //             $registros = $registros->reject(function ($item) use ($solicitud_id) {
    //                 return $item->fisioterapia_solicitud_id == $solicitud_id;
    //             })->values();
    //             $totales = $totales + $solicitudes[$con]['total'];
    //             $con++;
    //         }

    //         $pdf = Pdf::loadView('reporte_general_sesiones', compact('logo', 'fecha_inicio', 'fecha_fin', 'solicitudes', 'totales'))
    //             ->setPaper('A4', 'landscape');
    //         return $pdf->stream('reporte_general_sesiones.pdf');
    //     } catch (Throwable $e) {
    //         return ErrorHandler::handle($e);
    //     }
    // }
}
