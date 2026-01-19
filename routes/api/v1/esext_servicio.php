<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EsextServicioController;

Route::prefix('/v1/servicio/externo')->group(function () {
    // Operaciones de lectura
    Route::middleware(['permisos:servext-servicios-externos-lectura'])->group(function () {
        Route::get('/', [EsextServicioController::class, 'index']);
        Route::get('/{servicioId}', [EsextServicioController::class, 'show']);
    });

    // Operaciones de escritura (Creación, Actualización, Baja, Reincorporación)
    Route::middleware(['permisos:servext-servicios-externos-escritura'])->group(function () {
        Route::post('/', [EsextServicioController::class, 'store']);
        Route::put('/{servicioId}', [EsextServicioController::class, 'update']);
        Route::patch('/{servicioId}', [EsextServicioController::class, 'update']);
        Route::patch('/baja/{servicioId}', [EsextServicioController::class, 'baja']);
        Route::patch('/reincorporar/{servicioId}', [EsextServicioController::class, 'reincorporar']);
    });

    // Operaciones de eliminación
    Route::middleware(['permisos:servext-servicios-externos-escritura'])->group(function () {
        Route::delete('/{servicioId}', [EsextServicioController::class, 'destroy']);
    });
});
