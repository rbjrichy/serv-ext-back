<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EsextServicioInstitucion;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EsextServicioInstitucionController extends Controller
{
    public function addInstitucionServicio(Request $request)
    {
        try {
            // return response()->json($request->all());
            $validated = $request->validate([
                'esext_institucion_id' => ['required', Rule::exists('esext_instituciones', 'id')],
                'esext_servicio_id' => ['required', Rule::exists('esext_servicios', 'id')],
                'observaciones' => 'nullable|string',
                'costo' => 'nullable|numeric',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'estado_id' => ['required', Rule::exists('estado_ocurrencias', 'id')],
                'entidad_id' => 'nullable|exists:entidads,id',
            ]);
            $user = Auth::getPayload()->get('user');
            // Crear o recuperar la relación
            $relacion = EsextServicioInstitucion::firstOrCreate(
                [
                    'esext_institucion_id' => $validated['esext_institucion_id'],
                    'esext_servicio_id' => $validated['esext_servicio_id'],
                ],
                [
                    'observaciones' => $validated['observaciones'] ?? null,
                    'costo' => $validated['costo'] ?? null,
                    'fecha_inicio' => $validated['fecha_inicio'] ?? now(),
                    'fecha_fin' => $validated['fecha_fin'] ?? null,
                    'estado_id' => $validated['estado_id'],
                    'usr_alta' => $user,
                    'fecha_registro' => now(),
                    'entidad_id' => $validated['entidad_id'] ?? null,
                ]
            );
            if ($relacion->wasRecentlyCreated) {
                return response()->json([
                    'error' => false,
                    'message' => 'Servicio añadido correctamente a la institución.',
                    'data' => $relacion,
                    'status' => Response::HTTP_CREATED
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'error' => false,
                    'message' => 'El servicio ya pertenecia a la institución.',
                    'data' => $relacion,
                    'status' => Response::HTTP_CREATED
                ], Response::HTTP_CREATED);
            }


        } catch (ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Errores de validación.',
                'errors' => $e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error al añadir el servicio a la institución: ' . $e->getMessage(),
                'data' => [],
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Endpoint para dar de baja un servicio
     */
    public function baja(Request $request, $instiServicioId)
    {
        // return response()->json($request->all());
        try {
            $instiServicio = EsextServicioInstitucion::find($instiServicioId);

            if (!$instiServicio) {
                throw new \Exception('La relación Intitución Servicio no existe', 404);
            }

            $validated = $request->validate([
                'observaciones_baja' => 'required|string|max:200',
            ]);
            $user = Auth::getPayload()->get('user');

            $observaciones = $validated['observaciones_baja'];//strtoupper($validated['observaciones_baja']);
            $instiServicio->update([
                'estado_id' => 33, // DESHABILITADO
                'observaciones_baja' => $observaciones,
                'usr_mod' => $user,
            ]);
            return response()->json($instiServicio);
            return response()->json([
                'error' => false,
                'data' => $instiServicio,
                'mensaje' => 'Relacion Intitución Servicio dado de baja correctamente',
                'status' => Response::HTTP_OK,
            ], Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'Error de validación al dar de baja la relacion Institución Servicio.',
                'detalles' => $e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() > 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'No se pudo dar de baja la relacion Institución Servicio: '. $e->getMessage(),
                'status' => $statusCode,
            ], $statusCode);
        }
    }

    /**
     * Endpoint para reincorporar un servicio
     */
    public function reincorporar(Request $request, $instiServicioId)
    {
        try {
            $instiServicio = EsextServicioInstitucion::find($instiServicioId);

            if (!$instiServicio) {
                throw new \Exception('La relación Intitución Servicio no existe', 404);
            }

            $validated = $request->validate([
                'observaciones_reincorporar' => 'required|string|max:200',
            ]);
            $user = Auth::getPayload()->get('user');
            $instiServicio->update([
                'estado_id' => 32, // ID para estado HABILITADO
                'observaciones_reincorporar' => $validated['observaciones_reincorporar'],
                'usr_mod' => $user, // Usar usr_mod para el usuario que reincorpora
            ]);

            return response()->json([
                'error' => false,
                'data' => $instiServicio,
                'mensaje' => 'Relacion Intitución Servicio reincorporado correctamente',
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'Error de validación al reincorporar la relacion Institución Servicio.',
                'detalles' => $e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() > 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
            return response()->json([
                'error' => false,
                'mensaje' => 'Error al reincorporar la relacion Institución Servicio: ' . $e->getMessage(),
                'status' => $statusCode
            ], $statusCode);
        }
    }

    function actualizarpivot(Request $request, $instiServicioId) {
        try {
            $instiServicio = EsextServicioInstitucion::find($instiServicioId);
            if (!$instiServicio) {
                throw new \Exception('La relación Intitución Servicio no existe', 404);
            }
            $validated = $request->validate([
                'costo' => 'nullable|numeric|min:0',
                'fecha_inicio' => 'nullable|date|after_or_equal:today',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'estado_id' => [
                    'required',
                    Rule::exists('estado_ocurrencias', 'id'),
                    Rule::in([32, 33, 34, 35]),
                ],
                'entidad_id' => 'nullable|exists:entidads,id',
            ]);

            $user = Auth::getPayload()->get('user');
            $instiServicio->update([
                'costo' => $validated['costo'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'],
                'estado_id' => $validated['estado_id'],
                'entidad_id' => $validated['entidad_id'],
                'usr_mod' => $user,
            ]);

            return response()->json([
                'error' => false,
                'data' => $instiServicio,
                'mensaje' => 'Relacion Intitución Servicio se actualizao los datos correctamente',
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() > 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
            return response()->json([
                'error' => false,
                'mensaje' => 'Error al actualizar los datos en la relacion Institucion Servicio: ' . $e->getMessage(),
                'status' => $statusCode
            ], $statusCode);
        }
    }

}
