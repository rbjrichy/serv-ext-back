<?php

namespace App\Http\Controllers;

use App\Common\ErrorHandler;
use App\Http\Requests\Comoon\StoreObservacionRequest;
use App\Http\Requests\LaboratorioCategoria\StoreLaboratorioCategoriaRequest;
use App\Models\LaboratorioCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LaboratorioCategoriaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = LaboratorioCategoria::buscar($request);
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de categorias laboratorio',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function listar_subcategorias(Request $request)
    {
        try {
            $data = LaboratorioCategoria::listar_subcategorias($request);
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de categorias y subcategorias de laboratorio',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function get_one($id)
    {
        try {
            $data = LaboratorioCategoria::where('id', $id)->first();
            if (!$data) {
                throw new HttpException(404, 'La categoria no existe');
            }
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de categoria laboratorio',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function store(StoreLaboratorioCategoriaRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::getPayload()->get('user');
            $estado_id = 17; // VISIBLE
            $validado = $request->validated();

            $categoria = LaboratorioCategoria::where('nombre', $validado['nombre'])
                ->first();
            if ($categoria) {
                throw new HttpException(409, 'La categoria ya fue creada');
            }


            $validado['nombre'] = strtoupper($validado['nombre']);
            $validado['estado_id'] = $estado_id;
            $validado['usr_alta'] = $user;


            $data = LaboratorioCategoria::create($validado);

            if (!$data) {
                throw new HttpException(409, 'Ocurrio un problema al registrar la accion');
            }


            DB::commit();
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Categoria creada con exito',
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }

    public function update($id, StoreLaboratorioCategoriaRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::getPayload()->get('user');
            $validado = $request->validated();
            $categoria = LaboratorioCategoria::find($id);
            if (!$categoria) {
                throw new HttpException(404, 'No se encontro la categoria');
            }

            $categoria_repetida = LaboratorioCategoria::where('nombre', $validado['nombre'])
                ->where('id', '<>', $id)
                ->first();
            if ($categoria_repetida) {
                throw new HttpException(409, 'La categoria ya fue registrada');
            }


            $validado['nombre'] = strtoupper($validado['nombre']);
            $validado['usr_mod'] = $user;


            $data = $categoria->update($validado);

            if (!$data) {
                throw new HttpException(409, 'Ocurrio un problema al registrar la accion');
            }


            DB::commit();
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Categoria actualizada con exito',
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }

    public function baja($id, StoreObservacionRequest $request)
    {
        try {
            $user = Auth::getPayload()->get('user');
            $data = LaboratorioCategoria::find($id);
            $validado = $request->validated();

            $validado['observaciones'] = strtoupper($validado['observaciones']);

            if (!$data) {
                throw new HttpException(404, 'Actividad no encontrada');
            }

            $data->update([
                'estado_id' => 18, //ANULADO
                'usr_mod' => $user,
                'observaciones_baja' => $validado['observaciones']
            ]);

            return response()->json([
                'error' => false,
                'data' => null,
                'mensaje' => 'Acción Exitosa',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function reincorporar($id, StoreObservacionRequest $request)
    {
        try {
            $user = Auth::getPayload()->get('user');
            $data = LaboratorioCategoria::find($id);
            $validado = $request->validated();

            $validado['observaciones'] = strtoupper($validado['observaciones']);

            if (!$data) {
                throw new HttpException(404, 'Actividad no encontrada');
            }

            $data->update([
                'estado_id' => 17, //VISIBLE
                'usr_mod' => $user,
                'observaciones_reincorporar' => $validado['observaciones']
            ]);

            return response()->json([
                'error' => false,
                'data' => null,
                'mensaje' => 'Acción Exitosa',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }
}
