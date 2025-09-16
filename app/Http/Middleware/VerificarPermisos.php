<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermisos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        try {
            // Obtener el token y decodificar el payload
            $permisos = Auth::getPayload()->get('permisos');
            // Verificar si el permiso existe en el array de permisos
            if (!is_array($permisos) || !in_array($permiso, $permisos)) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'No autorizado: no tiene los permisos requeridos',
                    'data' => null,
                    'status' => 409
                ], 409);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'No autorizado: token inválido o no proporcionado',
                'data' => null,
                'status' => 409
            ], 409);
        }
    }
}
