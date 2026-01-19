<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EsextInstitucion;
use App\Models\EsextServicio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LDAP\Result;

class EsExtSolicitudVentaServicioController extends Controller
{
    /**
     * Listar instituciones con paginación y filtros
     */
    public function index(Request $request)
    {
        try {
            $result = ['error'=>'ningun error'];
            return response()->json([
                'error' => false,
                'data' => $result,
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener las solicitudes de venta de servicios ' . $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function listaServiciosAsignados() {
        try {
            $result = ['error'=>'ningun error', 'data'=>'lista de servicios externos asignados'];
            return response()->json([
                'error' => false,
                'data' => $result,
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener los servicios externos del paciente seleccionado' . $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Crear una nueva institución
     */
    public function store(Request $request)
    {
        try {
            $mensajes = [
                'institucion.unique' => 'La :attribute ingresada ya se encuentra registrada en el sistema. Por favor, ingrese un nombre de institución diferente.',
                'institucion.required' => 'El campo :attribute es obligatorio.',
                'institucion.max' => 'El campo :attribute no debe exceder los 80 caracteres.',
            ];
            $validated = $request->validate([
                'institucion' => 'required|string|max:80|unique:esext_instituciones,institucion',
                'observaciones' => 'nullable|string|max:200',
                'telefono' => 'nullable|string|max:80',
                'direccion' => 'nullable|string|max:200',
                // 'estado_id' => 'nullable|integer|exists:estado_ocurrencias,id',
                'fecha_registro' => 'nullable|date',
            ],$mensajes);

            $user = Auth::getPayload()->get('user');

            // Mapear nombre -> institucion para la base de datos
            $dbData = [
                'institucion' => $validated['institucion'],
                'observaciones' => $validated['observaciones'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'estado_id' => $validated['estado_id'] ?? 32, // Estado HABILITADO por defecto
                'usr_alta' => $user,
                'fecha_registro' => $validated['fecha_registro'] ?? now(),
            ];

            $institucion = EsextInstitucion::create($dbData);

            // return response()->json($responseData, Response::HTTP_CREATED);
            return response()->json([
                'error' => false,
                'data' => $institucion,
                'mensaje' => 'Institución creada correctamente',
                'status' => Response::HTTP_CREATED,
            ],Response::HTTP_CREATED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'error de validación '.$e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'Error al crear la institución '.$e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mostrar una institución específica
     */
    public function show($instiExterna)
    {
        try {
            $institucion = EsextInstitucion::find($instiExterna);
            if (!$institucion) {
                throw new Exception('No existe la institución');
            }

            // Transformar para el frontend
            $responseData = [
                'id' => $institucion->id,
                'institucion' => $institucion->institucion,
                'observaciones' => $institucion->observaciones,
                'telefono' => $institucion->telefono,
                'direccion' => $institucion->direccion,
                'estado_id' => $institucion->estado_id,
                'usr_alta' => $institucion->usr_alta,
                'usr_mod' => $institucion->usr_mod,
                'fecha_registro' => $institucion->fecha_registro,
                'created_at' => $institucion->created_at,
                'updated_at' => $institucion->updated_at,
                'observaciones_baja' => $institucion->observaciones_baja ?? null,
                'observaciones_reincorporar' => $institucion->observaciones_reincorporar ?? null,
            ];

            return response()->json([
                'error' => false,
                'data' => $responseData,
                'mensaje' => 'Institucion encontrada',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener la institución '.$e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualizar una institución
     */
    public function update(Request $request, $instiExterna)
    {
        try {
            $institucion = EsextInstitucion::find($instiExterna);

            if (!$institucion) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Institución no encontrada',
                    'status' => Response::HTTP_NOT_FOUND
                ], Response::HTTP_NOT_FOUND);
            }
            $user = Auth::getPayload()->get('user');
            $validated = $request->validate([
                'institucion' => 'sometimes|required|string|max:80',
                'observaciones' => 'nullable|string|max:200',
                'telefono' => 'nullable|string|max:80',
                'direccion' => 'nullable|string|max:200',
                'estado_id' => 'nullable|integer|exists:estado_ocurrencias,id',
                // 'usr_mod' => 'nullable|string|max:50',
                'observaciones_baja' => 'nullable|string|max:200',
                'observaciones_reincorporar' => 'nullable|string|max:200',
            ]);
            $user = Auth::getPayload()->get('user');
            $validated['usr_mod'] = $user;
            $institucion->update($validated);

            return response()->json([
                'error' => false,
                'data' => $institucion,
                'mensaje' => 'Institución actualizada correctamente',
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error de validación',
                'detalles' => $e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al actualizar la institución',
                'detalles' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Eliminar una institución
     */
    public function destroy($instiExterna)
    {
        try {
            $institucion = EsextInstitucion::find($instiExterna);

            if (!$institucion) {
                // return response()->json(['error' => 'Institución no encontrada'], Response::HTTP_NOT_FOUND);
                throw new \Exception('Institución no encontrada', Response::HTTP_NOT_FOUND);
            }

            $institucion->delete();

            return response()->json([
                'error' => false,
                'data' => $instiExterna,
                'mensaje' => 'Institución eliminada correctamente',
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al eliminar la institución ' . $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Endpoint para dar de baja una institución
     */
    public function baja(Request $request, $instiExterna)
    {
        try {
            $institucion = EsextInstitucion::find($instiExterna);

            if (!$institucion) {
                throw new \Exception('Institución no encontrada', 404);
            }

            $validated = $request->validate([
                'observaciones_baja' => 'required|string|max:200',
            ]);

            $user = Auth::getPayload()->get('user');
            // $observaciones = strtoupper($validated['observaciones_baja']);

            $institucion->update([
                'estado_id' => 33, // DESHABILITADO
                'observaciones_baja' => $validated['observaciones_baja'],
                'usr_mod' => $user,
            ]);

            return response()->json([
                'error' => false,
                'data' => $institucion,
                'mensaje' => 'Institución dada de baja correctamente',
                'status' => 200,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'No se pudo dar de baja a la institución '. $e->getMessage(),
                'status' => $e->getCode() ?: 500,
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Endpoint para reincorporar una institución
     */
    public function reincorporar(Request $request, $instiExterna)
    {
        try {
            $institucion = EsextInstitucion::find($instiExterna);

            if (!$institucion) {
                throw new \Exception('Institución no encontrada');
            }

            $validated = $request->validate([
                'observaciones_reincorporar' => 'required|string|max:200',
            ]);
            $user = Auth::getPayload()->get('user');
            $institucion->update([
                'estado_id' => 32, // ID para estado HABILITADO
                'observaciones_reincorporar' => $validated['observaciones_reincorporar'],
                'usuario' => $user,
            ]);

            return response()->json([
                'error' => false,
                'data' => $institucion,
                'mensaje' => 'Institución reincorporada correctamente',
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'mensaje' => 'Error al reincorporar la institución ' . $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    function getCbxInstituciones($estadoId = 32) {
        // $instituciones = EsextInstitucion::all()->orderBy('institucion')->pluck('institucion', 'id');
        $instituciones = EsextInstitucion::where('estado_id', $estadoId)->orderBy('institucion')->pluck('institucion', 'id');
        // $instituciones = EsextInstitucion::orderBy('institucion')->pluck('institucion', 'id');
        // return response()->json([
        //     'error' => true,
        //     'mensaje' => $instituciones,
        //     'status' => Response::HTTP_OK
        // ],Response::HTTP_OK);
        return response()->json($instituciones);
    }

    public function getServiciosInstitucion($institucionId)
    {
        try {
            $institucion = EsextInstitucion::with([
                'serviciosInstitucion.servicio', // carga los datos del servicio
                'serviciosInstitucion.estado'    // carga el estado de la relación pivote
            ])->find($institucionId);

            if (!$institucion) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Institución no encontrada',
                    'data' => [],
                    'status' => Response::HTTP_NOT_FOUND
                ], Response::HTTP_NOT_FOUND);
            }

            $servicios = $institucion->serviciosInstitucion->map(function ($rel) {
                return [
                    'id' => $rel->id,
                    'esext_institucion_id' => $rel->esext_institucion_id,
                    'esext_servicio_id' => $rel->esext_servicio_id,
                    'servicio' => $rel->servicio->servicio ?? null,
                    'observaciones' => $rel->observaciones,
                    'observaciones_baja' => $rel->observaciones_baja,
                    'observaciones_reincorporar' => $rel->observaciones_reincorporar,
                    'costo' => $rel->costo,
                    'fecha_inicio' => $rel->fecha_inicio,
                    'fecha_fin' => $rel->fecha_fin,
                    'estado_id' => $rel->estado_id,
                    'estado' => $rel->estado?->descripcion,
                    'usr_alta' => $rel->usr_alta,
                    'usr_mod' => $rel->usr_mod,
                    'fecha_registro' => $rel->fecha_registro,
                    'entidad_id' => $rel->entidad_id,
                    'created_at' => $rel->created_at,
                    'updated_at' => $rel->updated_at,
                ];
            });

            return response()->json([
                'error' => false,
                'data' => $servicios,
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener los servicios asociados a la institución: ' . $e->getMessage(),
                'data' => [],
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    function getListAddServiciosInstitucion($institucionId) {
        try {
            $institucion = EsextInstitucion::with(['serviciosInstitucion.servicio'])
                                            ->find($institucionId);
            $servicioIds = $institucion->serviciosInstitucion
                                            ->pluck('servicio.id')
                                            ->filter()
                                            ->unique()
                                            ->values();
            $serviciosCandidatos = EsextServicio::whereNotIn('id',$servicioIds)
                                                ->where('estado_id', 32)
                                                ->select('id', 'servicio')
                                                ->get();
            return response()->json([
                'error' => false,
                'data' => $serviciosCandidatos,
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener los servicios para añadir a la institución: ' . $e->getMessage(),
                'data' => [],
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function obtenerInstitucionesConServicios(): string
    {
        $sql = "
            SELECT
                CONCAT(
                    '[',
                    GROUP_CONCAT(
                        CONCAT(
                            '{',
                            '\"institucion_id\":', i.id,
                            ',\"institucion\":\"', i.institucion, '\"',
                            ',\"servicios\":',
                            (
                                SELECT CONCAT(
                                    '[',
                                    GROUP_CONCAT(
                                        CONCAT(
                                            '{',
                                            '\"servicio_id\":', s.id,
                                            ',\"servicio\":\"', s.servicio, '\"',
                                            '}'
                                        )
                                    ),
                                    ']'
                                )
                                FROM esext_servicios_instituciones si
                                INNER JOIN esext_servicios s
                                        ON s.id = si.esext_servicio_id
                                INNER JOIN estado_ocurrencias es
                                        ON s.estado_id = es.id
                                INNER JOIN estado_ocurrencias esi
                                        ON si.estado_id = esi.id
                                WHERE si.esext_institucion_id = i.id
                                  AND es.descripcion = 'HABILITADO'
                                  AND esi.descripcion = 'HABILITADO'
                            ),
                            '}'
                        )
                    ),
                    ']'
                ) AS instituciones_json
            FROM esext_instituciones i
            INNER JOIN estado_ocurrencias ei
                    ON i.estado_id = ei.id
            WHERE ei.descripcion = 'HABILITADO';
        ";

        $result = DB::select($sql);

        // Retorna el JSON generado por la consulta
        return $result[0]->instituciones_json ?? '[]';
    }

    public function getInstitucionesYServicios() {
        try {
            $instituciones = EsextInstitucion::whereHas('estado', function ($q) {
                $q->where('descripcion', 'HABILITADO');
            })->get(['id', 'institucion']);

            $servicios = collect();

            foreach ($instituciones as $institucion) {
                $serviciosInstitucion = $institucion->serviciosInstitucion()
                    ->whereHas('estado', function ($q) {
                        $q->where('descripcion', 'HABILITADO');
                    })
                    ->whereHas('servicio.estado', function ($q) {
                        $q->where('descripcion', 'HABILITADO');
                    })
                    ->get(['id', 'esext_institucion_id', 'esext_servicio_id']);

                foreach ($serviciosInstitucion as $servicio) {
                    $servicios->push([
                        'label' => $servicio->servicio->servicio,
                        'value' => $servicio->esext_servicio_id,
                        'institucionId' => $servicio->esext_institucion_id,
                    ]);
                }
            }

            $institucionesData = $instituciones->map(function ($item) {
                return [
                    'label' => $item->institucion,
                    'value' => $item->id,
                ];
            });

            $data = [
                'instituciones' => $institucionesData,
                'servicios' => $servicios,
            ];

            return response()->json([
                'error' => false,
                'data' => $data,
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener instituciones y servicios: ' . $e->getMessage(),
                'data' => [],
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getClasificacionesVisibles() {
        try {
            $clasificaciones = DB::table('clasificacion_servicios as cs')
                ->join('estado_ocurrencias as eo', 'cs.estado_id', '=', 'eo.id')
                ->where('eo.descripcion', 'visible')
                ->select('cs.id', 'cs.descripcion as clasificacion')
                ->get()
                ->map(function($item) {
                return [
                    'label' => $item->clasificacion,
                    'value' => $item->id,
                ];
            });


            return response()->json([
                'error' => false,
                'data' => $clasificaciones,
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener clasificaciones: ' . $e->getMessage(),
                'data' => [],
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
