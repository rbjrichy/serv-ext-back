<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        // return response()->json(['error msj' => 'llega al middleware correcto VerificarPermisos']);
        try {
            $token = $request->bearerToken();
            $payload = Auth::getPayload()->toArray();
            $permisos = $payload['permisos'] ?? [];

            // Log::info('Token recibido', ['token' => $token]);
            // Log::info('Payload decodificado', $payload);
            // Log::info('Permiso requerido', ['permiso' => $permiso]);

            if (!is_array($permisos) || !in_array($permiso, $permisos)) {
                Log::warning('Permiso denegado', ['permisos' => $permisos, 'requerido' => $permiso]);

                return response()->json([
                    'error' => true,
                    'mensaje' => 'No autorizado: no tiene los permisos requeridos',
                    'data' => null,
                    'status' => 409
                ], 409);
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error en verificación de permisos:', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => true,
                'mensaje' => 'No autorizado: token inválido o no proporcionado',
                'data' => null,
                'status' => 409
            ], 409);
        }
    }
}
