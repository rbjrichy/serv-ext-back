<?php
namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Common\ErrorHandler;
use App\Models\Afiliado;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AfiliadoController extends Controller
{
    public function buscar_afiliado(Request $request)
    {
        try {
            // return response()->json($request->all());
            // $data = Afiliado::buscar($request);
            $data = Afiliado::buscar($request);
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Lista de afiliados buscados',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function datos_paciente($id)
    {
        try {
            $data = Afiliado::datos_paciente($id);
            if (!$data) {
                throw new HttpException(404, 'Afiliado no encontrado');
            }
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Datos obtenidos',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }

    public function instituciones()
    {
        try {
            $data = Afiliado::instituciones();
            return response()->json([
                'error' => false,
                'data' => $data,
                'mensaje' => 'Datos obtenidos',
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return ErrorHandler::handle($e);
        }
    }
}
