<?php

namespace App\Http\Controllers;

use App\Common\ErrorHandler;
use App\Models\Afiliado;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AfiliadoController extends Controller
{
    function index(Request $request)
    {
        try {
            $data = Afiliado::buscar($request);
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de afiliados',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    function datos_paciente($id)
    {
        try {
            $data = Afiliado::datos_paciente($id);
            if(!$data){
                throw new HttpException(404, 'Afiliado no encontrado');
            }
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Datos obtenidos',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    function instituciones()
    {
        try {
            $data = Afiliado::instituciones();
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Datos obtenidos',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }
}
