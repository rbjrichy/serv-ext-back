<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Common\ErrorHandler;
use App\Models\FiMedEsp;
use Illuminate\Http\Request;

class FiMedEspController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = FiMedEsp::buscar($request);
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de especialidades activas',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }
}
