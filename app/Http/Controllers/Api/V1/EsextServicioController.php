<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EsextServicio;
use App\Models\ServicioExterno;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EsextServicioController extends Controller
{
    /**
     * Listar servicios con paginación y filtros
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $servicio = $request->get('servicio'); // Filtro por el nombre del servicio

            $query = EsextServicio::query();

            // Aplicar filtro por servicio
            if ($servicio) {
                $query->where('servicio', 'like', '%' . $servicio . '%');
            }

            // Obtener datos paginados
            $servicios = $query->orderBy('id', 'desc')
                                ->paginate($perPage, ['*'], 'page', $page);

            // Transformar los datos para el frontend
            $transformedData = $servicios->getCollection()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'servicio' => $item->servicio,
                    'observaciones' => $item->observaciones,
                    'estado_id' => $item->estado_id,
                    'usr_alta' => $item->usr_alta,
                    'usr_mod' => $item->usr_mod,
                    'fecha_registro' => $item->fecha_registro,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    // Campos adicionales
                    'observaciones_baja' => $item->observaciones_baja ?? null,
                    'observaciones_reincorporar' => $item->observaciones_reincorporar ?? null,
                ];
            });

            return response()->json([
                'error' => false,
                'data' => $transformedData,
                'meta' => [
                    'total' => $servicios->total(),
                    'current_page' => $servicios->currentPage(),
                    'per_page' => $servicios->perPage(),
                    'last_page' => $servicios->lastPage(),
                    'from' => $servicios->firstItem(),
                    'to' => $servicios->lastItem(),
                ],
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener los servicios ' . $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Crear un nuevo servicio
     */
    public function store(Request $request)
    {
        try {

            $mensajes = [
                'servicio.unique' => 'El :attribute ingresado ya se encuentra registrado en el sistema. Por favor, ingrese un nombre de servicio diferente.',
                'servicio.required' => 'El campo :attribute es obligatorio.',
            ];

            $validated = $request->validate([
                'servicio' => 'required|string|max:80|unique:esext_servicios,servicio',
                'observaciones' => 'nullable|string|max:200',
                'fecha_registro' => 'nullable|date',
            ],$mensajes);

            $user = Auth::getPayload()->get('user');

            $dbData = [
                'servicio' => $validated['servicio'],
                'observaciones' => $validated['observaciones'] ?? null,
                'estado_id' => 32, // Estado HABILITADO por defecto
                'usr_alta' => $user,
                'fecha_registro' => $validated['fecha_registro'] ?? now(),
            ];

            $servicio = EsextServicio::create($dbData);

            return response()->json([
                'error' => false,
                'data' => $servicio,
                'mensaje' => 'Servicio creado correctamente',
                'status' => Response::HTTP_CREATED,
            ],Response::HTTP_CREATED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'Error de validación: Verifique los datos ingresados.',
                'detalles' => $e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'Error al crear el servicio: '.$e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mostrar un servicio específico
     */
    public function show($servicioId)
    {
        try {
            $servicio = EsextServicio::find($servicioId);
            if (!$servicio) {
                throw new Exception('No existe el servicio');
            }

            // Transformar para el frontend
            $responseData = [
                'id' => $servicio->id,
                'servicio' => $servicio->servicio,
                'observaciones' => $servicio->observaciones,
                'estado_id' => $servicio->estado_id,
                'usr_alta' => $servicio->usr_alta,
                'usr_mod' => $servicio->usr_mod,
                'fecha_registro' => $servicio->fecha_registro,
                'created_at' => $servicio->created_at,
                'updated_at' => $servicio->updated_at,
                'observaciones_baja' => $servicio->observaciones_baja ?? null,
                'observaciones_reincorporar' => $servicio->observaciones_reincorporar ?? null,
            ];

            return response()->json([
                'error' => false,
                'data' => $responseData,
                'mensaje' => 'Servicio encontrado',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener el servicio: '.$e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualizar un servicio
     */
    public function update(Request $request, $servicioId)
    {
        try {
            $servicio = EsextServicio::find($servicioId);

            if (!$servicio) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Servicio no encontrado',
                    'status' => Response::HTTP_NOT_FOUND
                ], Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'servicio' => 'sometimes|required|string|max:80',
                'observaciones' => 'nullable|string|max:200',
                'estado_id' => 'nullable|integer|exists:estado_ocurrencias,id',
                'observaciones_baja' => 'nullable|string|max:200',
                'observaciones_reincorporar' => 'nullable|string|max:200',
            ]);
            //evitar que se cambie el nombre a servicio
            if (array_key_exists('servicio', $validated)) {
                unset($validated['servicio']);
            }
            $user = Auth::getPayload()->get('user');
            $validated['usr_mod'] = $user;
            $servicio->update($validated);

            return response()->json([
                'error' => false,
                'data' => $servicio,
                'mensaje' => 'Servicio actualizado correctamente',
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
                'mensaje' => 'Error al actualizar el servicio',
                'detalles' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Eliminar un servicio
     */
    public function destroy($servicioId)
    {
        try {
            $servicio = EsextServicio::find($servicioId);

            if (!$servicio) {
                throw new \Exception('Servicio no encontrado', Response::HTTP_NOT_FOUND);
            }

            $servicio->delete();

            return response()->json([
                'error' => false,
                'data' => $servicioId,
                'mensaje' => 'Servicio eliminado correctamente',
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            $statusCode = $e->getCode() > 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
            return response()->json([
                'error' => true,
                'message' => 'Error al eliminar el servicio: ' . $e->getMessage(),
                'status' => $statusCode
            ], $statusCode);
        }
    }

    /**
     * Endpoint para dar de baja un servicio
     */
    public function baja(Request $request, $servicioId)
    {
        try {
            $servicio = EsextServicio::find($servicioId);

            if (!$servicio) {
                throw new \Exception('Servicio no encontrado', 404);
            }

            $validated = $request->validate([
                'observaciones_baja' => 'required|string|max:200',
            ]);

            $user = Auth::getPayload()->get('user');
            // $observaciones = strtoupper($validated['observaciones_baja']);

            $servicio->update([
                'estado_id' => 33, // DESHABILITADO
                'observaciones_baja' => $validated['observaciones_baja'],
                'usr_mod' => $user,
            ]);

            return response()->json([
                'error' => false,
                'data' => $servicio,
                'mensaje' => 'Servicio dado de baja correctamente',
                'status' => Response::HTTP_OK,
            ], Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'Error de validación al dar de baja.',
                'detalles' => $e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() > 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'No se pudo dar de baja al servicio: '. $e->getMessage(),
                'status' => $statusCode,
            ], $statusCode);
        }
    }

    /**
     * Endpoint para reincorporar un servicio
     */
    public function reincorporar(Request $request, $servicioId)
    {
        try {
            $servicio = EsextServicio::find($servicioId);

            if (!$servicio) {
                throw new \Exception('Servicio no encontrado', 404);
            }

            $validated = $request->validate([
                'observaciones_reincorporar' => 'required|string|max:200',
            ]);
            $user = Auth::getPayload()->get('user');
            $servicio->update([
                'estado_id' => 32, // ID para estado HABILITADO
                'observaciones_reincorporar' => $validated['observaciones_reincorporar'],
                'usr_mod' => $user, // Usar usr_mod para el usuario que reincorpora
            ]);

            return response()->json([
                'error' => false,
                'data' => $servicio,
                'mensaje' => 'Servicio reincorporado correctamente',
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'data' => null,
                'mensaje' => 'Error de validación al reincorporar.',
                'detalles' => $e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() > 0 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
            return response()->json([
                'error' => false,
                'mensaje' => 'Error al reincorporar el servicio: ' . $e->getMessage(),
                'status' => $statusCode
            ], $statusCode);
        }
    }
}