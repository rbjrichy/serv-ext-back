<?php

namespace App\Services;

use App\Exceptions\DuplicateEntryException;
use Throwable;

class ErrorHandlerService
{
    public static function handleDatabaseError(Throwable $e)
    {
        $codigo = $e->getCode();
        $mensaje = $e->getMessage();

        // Error de duplicado
        if ($codigo === 23000 || str_contains($mensaje, '1062')) {
            // Extraer el nombre duplicado del mensaje
            preg_match("/Duplicate entry '(.+?)'/", $mensaje, $matches);
            $nombreDuplicado = $matches[1] ?? '';

            // Limpiar el nombre para mostrar solo la parte relevante
            $nombreLimpio = self::limpiarNombreDuplicado($nombreDuplicado);

            throw new DuplicateEntryException(
                "Ya existe una institución activa con el nombre: '{$nombreLimpio}'",
                'INSTITUTION_DUPLICATE'
            );
        }
        // Error de conexión a base de datos
        if (str_contains($mensaje, 'Connection') || str_contains($mensaje, 'SQLSTATE[HY000]')) {
            throw new \Exception(
                'Error de conexión con la base de datos',
                'DB_CONNECTION_ERROR'
            );
        }

        // Error de timeout
        if (str_contains($mensaje, 'timed out')) {
            throw new \Exception(
                'La operación tardó demasiado tiempo',
                'TIMEOUT_ERROR'
            );
        }
        // Error de integridad referencial
        if (str_contains($mensaje, '1451') || str_contains($mensaje, 'Integrity constraint')) {
            throw new \Exception(
                'No se puede completar la operación debido a registros relacionados',
                'INTEGRITY_CONSTRAINT_ERROR'
            );
        }
        // Otros errores de base de datos
        throw new \Exception('Error de base de datos', $codigo);
    }

    private static function limpiarNombreDuplicado(string $nombre): string
    {
        // Remover el "-1" que indica el valor de "activo"
        return preg_replace('/-\d+$/', '', $nombre);
    }

    public static function getGenericErrorMessage(): array
    {
        return [
            'message' => 'Error inesperado en el servidor',
            'error_code' => 'INTERNAL_SERVER_ERROR'
        ];
    }
}