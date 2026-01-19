<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstitucionExternaRequest;
use App\Http\Requests\UpdateInstitucionExternaRequest;
use App\Http\Resources\InstitucionExternaResource;
use App\Models\InstitucionExterna;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\DuplicateEntryException;
use App\Services\ErrorHandlerService;

class InstitucionExternaController extends Controller
{
/**
     * Listado paginado con filtros básicos (nombre, estado).
     * GET /api/instituciones-externas
     */
    public function index(Request $request)
    {
        $q = InstitucionExterna::query();

        // Búsqueda por nombre
        if ($request->filled('q')) {
            $term = $request->input('q');
            $q->where('nombre', 'like', "%{$term}%");
        }

        // Filtrar por estado
        if ($request->filled('estado')) {
            $q->where('estado', $request->input('estado'));
        }

        // Incluir soft-deleted si se pide
        if ($request->boolean('with_trashed')) {
            $q->withTrashed();
        }

        // Orden y paginación
        $perPage = (int) $request->input('per_page', 15);
        $orderBy = $request->input('order_by', 'id');
        $orderDir = $request->input('order_dir', 'desc');

        $items = $q->orderBy($orderBy, $orderDir)->paginate($perPage);

        return InstitucionExternaResource::collection($items)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created resource.
     * POST /api/instituciones-externas
     */
    public function store(StoreInstitucionExternaRequest $request)
    {
        $request['estado_id'] = 17;
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $institucion = InstitucionExterna::create($data);
            DB::commit();

            return (new InstitucionExternaResource($institucion))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);

        } catch (DuplicateEntryException $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getCustomMessage(),
                'error_code' => $e->getErrorCode(),
                'suggestion' => 'Por favor, verifique el nombre o contacte al administrador para reactivar la institución existente.'
            ], Response::HTTP_CONFLICT); // 409 Conflict es más apropiado

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creando institución: ' . $e->getMessage());

            // Manejar otros errores con el servicio
            try {
                ErrorHandlerService::handleDatabaseError($e);
            } catch (DuplicateEntryException $dupE) {
                return response()->json([
                    'message' => $dupE->getCustomMessage(),
                    'error_code' => $dupE->getErrorCode()
                ], Response::HTTP_CONFLICT);
            } catch (\Exception $genericE) {
                return response()->json([
                    'message' => 'Error al procesar la solicitud.',
                    'error_code' => 'PROCESSING_ERROR'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * Mostrar un recurso.
     * GET /api/instituciones-externas/{id}
     */
    public function show($id)
    {
        $institucion = InstitucionExterna::withTrashed()->find($id);

        if (!$institucion) {
            return response()->json(['message' => 'Institución no encontrada.'], Response::HTTP_NOT_FOUND);
        }

        return (new InstitucionExternaResource($institucion))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Actualizar recurso.
     * PUT/PATCH /api/instituciones-externas/{id}
     */
    public function update(UpdateInstitucionExternaRequest $request, $id)
    {
        $institucion = InstitucionExterna::withTrashed()->find($id);

        if (!$institucion) {
            return response()->json(['message' => 'Institución no encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validated();

        DB::beginTransaction();
        try {
            $institucion->fill($data);
            $institucion->save();
            DB::commit();

            return (new InstitucionExternaResource($institucion))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error updating InstitucionExterna: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al actualizar la institución externa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Soft delete.
     * DELETE /api/instituciones-externas/{id}
     */
    public function destroy($id)
    {
        $institucion = InstitucionExterna::find($id);

        if (!$institucion) {
            return response()->json(['message' => 'Institución no encontrada.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $institucion->delete();

            return response()->json([
                'message' => 'Institución eliminada (soft delete).'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Error deleting InstitucionExterna: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al eliminar la institución externa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Restaurar soft deleted.
     * POST /api/instituciones-externas/{id}/restore
     */
    public function restore($id)
    {
        $institucion = InstitucionExterna::withTrashed()->find($id);

        if (!$institucion) {
            return response()->json(['message' => 'Institución no encontrada.'], Response::HTTP_NOT_FOUND);
        }

        if (!$institucion->trashed()) {
            return response()->json(['message' => 'La institución no está eliminada.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $institucion->restore();

            return (new InstitucionExternaResource($institucion))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Error restoring InstitucionExterna: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al restaurar la institución externa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Borrado físico forzado.
     * DELETE /api/instituciones-externas/{id}/force
     */
    public function forceDelete($id)
    {
        $institucion = InstitucionExterna::withTrashed()->find($id);

        if (!$institucion) {
            return response()->json(['message' => 'Institución no encontrada.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $institucion->forceDelete();

            return response()->json([
                'message' => 'Institución eliminada permanentemente.'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Error force deleting InstitucionExterna: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al eliminar permanentemente la institución externa.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
