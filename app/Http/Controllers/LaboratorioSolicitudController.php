<?php

namespace App\Http\Controllers;

use App\Common\ErrorHandler;
use App\Http\Requests\Comoon\StoreObservacionRequest;
use App\Http\Requests\LaboratorioSolicitud\StoreLaboratorioSolicitudRequest;
use App\Http\Requests\LaboratorioSolicitud\StoreProgramarLaboratorioSolicitudRequest;
use App\Models\Afiliado;
use App\Models\ConexLaboratorioExamen;
use App\Models\LaboratorioExamen;
use App\Models\LaboratorioSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LaboratorioSolicitudController extends Controller
{
    public function index(Request $request)
    {
        try {

            $data = LaboratorioSolicitud::buscar($request);
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de solicitudes',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function get_examenes_resultados($id)
    {
        try {
            $data = LaboratorioSolicitud::datos_solicitud($id);
            if (!$data) {
                throw new HttpException(404, 'No se encontro la solicitud');
            }
            $data = LaboratorioSolicitud::get_examenes_resultados($id);
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de solicitudes',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function datos_solicitud($id)
    {
        try {

            $data = LaboratorioSolicitud::datos_solicitud($id);
            if (!$data) {
                throw new HttpException(404, 'No se encontro la solicitud');
            }
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Datos Obtenidos',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function store(StoreLaboratorioSolicitudRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::getPayload()->get('user');
            $validado = $request->validated();
            $examenes_ids = [];
            $validado['diagnosticos'] = strtoupper($validado['diagnosticos']);
            $validado['observaciones'] = $validado['observaciones'] ? strtoupper($validado['observaciones']) : null;
            $validado['estado_id'] = 1;
            $validado['modulo_id'] = $validado['conex_id'] ? 1 : 5; // si hay conex_id es consulta externa sino laboratorio
            $validado['usr_alta'] = $user;

            $afiliado = Afiliado::datos_paciente($validado['paciente_id']);
            if (!$afiliado || $afiliado->estado_id != 1) { // Afiliado no encontrado o que no este de alta
                throw new HttpException(409, 'No se puede programar un laboratorio al paciente, se encuentra inhabilitado');
            }

            if ($validado['tipo_solicitud'] == 'examen') {
                $examenes_ids = LaboratorioExamen::examenes_subcategoria($validado['subcategorias']);
            } else {
                $examenes_ids = LaboratorioExamen::examenes_perfil($validado['perfil_id']);
            }
            unset($validado['conex_id']);
            unset($validado['subcategorias']);
            unset($validado['perfil_id']);
            $data = LaboratorioSolicitud::create($validado);
            if (!$data) {
                throw new HttpException(409, 'No se pudo crear la solicitud de laboratorio');
            }
            if (count($examenes_ids) == 0) {
                throw new HttpException(409, 'No se pudo crear la solicitud de laboratorio, no existen examenes asignados');
            }
            $data->examenes_solicitud()->sync($examenes_ids);
            DB::commit();
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Solicitud creada con exito',
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }

    public function cambio_estado($id, StoreObservacionRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::getPayload()->get('user');
            $data = LaboratorioSolicitud::find($id);
            $validado = $request->validated();

            $validado['observaciones'] = strtoupper($validado['observaciones']);

            if (!$data) {
                throw new HttpException(404, 'Solicitud no encontrada no disponible');
            }
            switch ($data->estado_id) {
                case 1:
                    $data->update([
                        'estado_id' => 18,
                        'usr_mod' => $user,
                        'observaciones_anulado' => $validado['observaciones']
                    ]);
                    break;
                case 2:
                    $data->update([
                        'estado_id' => 31,
                        'fecha_entrega' => null,
                        'usr_mod' => $user,
                        'observaciones_cambioestado' => $validado['observaciones']
                    ]);
                    break;
                default:
                    throw new HttpException(409, 'No se puede cambiar el estado de la solicitud de laboratorio');
            }
            DB::commit();
            return response()->json([
                'error' => false,
                'data' => null,
                'mensaje' => 'Acción Exitosa',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }

    public function programar($id, StoreProgramarLaboratorioSolicitudRequest $request)
    {
        try {
            $user = Auth::getPayload()->get('user');
            $data = LaboratorioSolicitud::find($id);
            $validado = $request->validated();
            if (!$data) {
                throw new HttpException(404, 'No se encontro la solicitud');
            }

            $data->update([
                'estado_id' => 31, //PROGRAMADO
                'usr_mod' => $user,
                'fecha_toma_muestra' => now(),
                'telefono_referencia' => $validado['telefono_referencia']
            ]);

            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Solicitud programada con exito',
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function llenado($id, Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::getPayload()->get('user');
            $examenes = $request->input('examenes');
            $data = LaboratorioSolicitud::find($id);
            if (!$data) {
                throw new HttpException(404, 'No se encontro la solicitud');
            }

            foreach ($examenes as $exa) {

                $examen = ConexLaboratorioExamen::find($exa['laboratorio_examen_id']);
                if (!$examen) {
                    throw new HttpException(409, 'No se pudo registrar los resultados');
                }
                $examen->resultado = $exa['resultado'];
                $examen->usr_mod = $user;
                $examen->save();
            }
            DB::commit();
            $examenes_pendientes = ConexLaboratorioExamen::where('laboratorio_solicitud_id', $id)
                ->whereNull('resultado')
                ->count();
            if ($examenes_pendientes == 0) {
                $data->update([
                    'estado_id' => 2, //ENTREGADO
                    'usr_mod' => $user,
                    'fecha_entrega' => now(),
                ]);
            }
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Solicitud llenada con exito',
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }

    // public function generar_informe($id, StoreGenerarInformeRequest $request)
    // {
    //     try {
    //         $user = Auth::getPayload()->get('user');
    //         $data = LaboratorioSolicitud::find($id);
    //         $validado = $request->validated();

    //         $validado['evolucion'] = strtoupper($validado['evolucion']);
    //         $validado['examen_fisico'] = strtoupper($validado['examen_fisico']);
    //         $validado['tratamiento'] = strtoupper($validado['tratamiento']);
    //         $validado['observaciones_fisio'] = strtoupper($validado['observaciones']);
    //         $validado['estado_id'] = 2; //ENTREGADO
    //         $validado['fecha_entregainforme'] = now();
    //         $validado['usr_mod'] = $user;
    //         unset($validado['observaciones']);
    //         if (!$data) {
    //             throw new HttpException(404, 'Sesion no encontrada ');
    //         }

    //         $data->update(
    //             $validado
    //         );

    //         return response()->json([
    //             'error' => false,
    //             'data' => null,
    //             'mensaje' => 'Acción Exitosa',
    //             'status' => 200,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return ErrorHandler::handle($e);
    //     }
    // }
}
