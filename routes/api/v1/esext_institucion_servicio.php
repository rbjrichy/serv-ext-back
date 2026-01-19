<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EsextInstitucionController;
use App\Http\Controllers\Api\V1\EsextServicioInstitucionController;

Route::prefix('v1')->group(function () {
    Route::prefix('institucion/servicio')->group(function () {
        //utils
        Route::get('/getcbxintituciones/{estadoId}', [EsextInstitucionController::class, 'getCbxInstituciones']);
        Route::get('/i-s/{institucionId}', [EsextInstitucionController::class, 'getServiciosInstitucion']);
        Route::get('/i-s/listadd/{institucionId}', [EsextInstitucionController::class, 'getListAddServiciosInstitucion']);
        // Operaciones de lectura
        // Route::middleware(['permisos:servext-instituciones-externas-lectura'])->group(function () {
        //     Route::get('/', [EsextInstitucionController::class, 'index']);
        //     Route::get('/{instiExterna}', [EsextInstitucionController::class, 'show']);
        // });

        // Operaciones de escritura
        Route::middleware(['permisos:servext-instituciones-externas-escritura'])->group(function () {
            Route::patch('/baja/{instiServicioId}', [EsextServicioInstitucionController::class, 'baja']);
            Route::patch('/reincorporar/{instiServicioId}', [EsextServicioInstitucionController::class, 'reincorporar']);
            Route::patch('/actualizar/pivot/{instiServicioId}', [EsextServicioInstitucionController::class, 'actualizarpivot']);
            Route::post('/i-s/add/servicio', [EsextServicioInstitucionController::class, 'addInstitucionServicio']);
        });

        // Operaciones de eliminación
        // Route::middleware(['permisos:servext-instituciones-externas-escritura'])->group(function () {
        //     Route::delete('/{instiExterna}', [EsextInstitucionController::class, 'destroy']);
        // });
    });
});
