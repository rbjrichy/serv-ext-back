<?php

namespace App\Common;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ErrorHandler
{
    /**
     * Maneja un error registrándolo y devolviendo una respuesta JSON.
     *
     * @param string $defaultMessage Mensaje por defecto para el usuario.
     * @param Throwable $error Objeto de error capturado.
     * @param int $statusCode Código de estado HTTP (por defecto 500).
     * @return \Illuminate\Http\JsonResponse
     */
    public static function handle(Throwable $error)
    {
        $censored_fields = ['password'];
        $messageError = '';
        $statusCode = '';
        if ($error instanceof HttpException) {
            $messageError = $error->getMessage();
            $statusCode = $error->getStatusCode();
        } else {
            $messageError = 'Ocurrio un error en el servidor';
            $statusCode = 500;
        }        // Loguear el error

        $body = Request::all();
        foreach ($body as $field => $value) {
            if (in_array($field, $censored_fields)) {
                unset($body[$field]);
            }
        }
        $data_log =  [
            'mensaje' => $error->getMessage(),
            'body' => $body,
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'ip' => Request::ip(),
            'archivo' => $error->getFile(),
            'linea' => $error->getLine(),
        ];

        if ($statusCode !== 500) {
            Log::channel('errores')->warning('Excepcion capturada:', $data_log);
        } else {
            Log::channel('errores')->error('Error capturado:', $data_log);
        }

        // Devolver respuesta JSON
        return response()->json([
            'error' => true,
            'mensaje' => $messageError,
            'data' => null,
            'status' => $statusCode
        ], $statusCode);
    }
}
