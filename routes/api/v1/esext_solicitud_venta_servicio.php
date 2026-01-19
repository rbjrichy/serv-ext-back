<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EsExtSolicitudVentaServicioController;

Route::prefix('v1')->group(function () {
    Route::prefix('solicitud/venta/servicio')->group(function () {
        //utils
        Route::get('/getInsitucionesConServicios', [EsExtSolicitudVentaServicioController::class, 'obtenerInstitucionesConServicios']);
        Route::get('/getInstitucionesServicios', [EsExtSolicitudVentaServicioController::class, 'getInstitucionesYServicios']);
        Route::get('/getClasificacionesVisibles', [EsExtSolicitudVentaServicioController::class, 'getClasificacionesVisibles']);
        // Operaciones de lectura
        Route::middleware(['permisos:servext-generar-solicitud-lectura'])->group(function () {
            Route::get('/', [EsExtSolicitudVentaServicioController::class, 'index']);
            Route::get('/{id}', [EsExtSolicitudVentaServicioController::class, 'show']);
        });

        // Operaciones de escritura
        Route::middleware(['permisos:servext-generar-solicitud-escritura'])->group(function () {
            Route::post('/', [EsExtSolicitudVentaServicioController::class, 'store']);
            Route::put('/{id}', [EsExtSolicitudVentaServicioController::class, 'update']);
        });

        // Operaciones de eliminación
        Route::middleware(['permisos:servext-generar-solicitud-escritura'])->group(function () {
            Route::delete('/{id}', [EsExtSolicitudVentaServicioController::class, 'destroy']);
        });
    });
});
