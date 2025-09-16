<?php

namespace App\Http\Controllers;

use App\Common\ErrorHandler;
use App\Http\Requests\Comoon\StoreObservacionRequest;
use App\Http\Requests\LaboratorioPerfil\StoreLaboratorioPerfilRequest;
use App\Models\LaboratorioPerfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LaboratorioPerfilController extends Controller
{
        public function index(Request $request)
    {
        try {
            $data = LaboratorioPerfil::buscar($request);
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de perfiles laboratorio',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function get_one($id)
    {
        try {
            $data = LaboratorioPerfil::where('id',$id)->first();
            if (!$data) {
                throw new HttpException(404, 'El perfil no existe');
            }
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de perfil laboratorio',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function store(StoreLaboratorioPerfilRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::getPayload()->get('user');
            $estado_id = 17; // VISIBLE
            $validado = $request->validated();

            $categoria = LaboratorioPerfil::where('nombre', $validado['nombre'])
                ->first();
            if ($categoria) {
                throw new HttpException(409, 'El perfil ya fue creada');
            }


            $validado['nombre'] = strtoupper($validado['nombre']);
            $validado['estado_id'] = $estado_id;
            $validado['usr_alta'] = $user;


            $data = LaboratorioPerfil::create($validado);

            $data->perfil_examenes()->sync($validado['subcategorias']);

            if (!$data) {
                throw new HttpException(409, 'Ocurrio un problema al registrar la accion');
            }


            DB::commit();
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Perfil creado con exito',
                'status' => 201,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }

    public function update($id, StoreLaboratorioPerfilRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::getPayload()->get('user');
            $validado = $request->validated();
            $categoria = LaboratorioPerfil::find($id);
            if (!$categoria) {
                throw new HttpException(404, 'No se encontro el perfil');
            }

            $categoria_repetida = LaboratorioPerfil::where('nombre', $validado['nombre'])
                ->where('id', '<>', $id)
                ->first();
            if ($categoria_repetida) {
                throw new HttpException(409, 'El perfil ya fue registrado');
            }


            $validado['nombre'] = strtoupper($validado['nombre']);
            $validado['usr_mod'] = $user;


            $data = $categoria->update($validado);

            $categoria->perfil_examenes()->sync($validado['subcategorias']);

            if (!$data) {
                throw new HttpException(409, 'Ocurrio un problema al registrar la accion');
            }


            DB::commit();
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Perfil actualizado con exito',
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
            $data = LaboratorioPerfil::find($id);
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
            $data = LaboratorioPerfil::find($id);
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
