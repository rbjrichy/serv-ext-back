<?php

namespace App\Http\Controllers;

use App\Common\ErrorHandler;
use App\Models\ClasificacionServicio;
use Illuminate\Http\Request;

class ClasificacionServicioController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = ClasificacionServicio::buscar($request);
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
}
