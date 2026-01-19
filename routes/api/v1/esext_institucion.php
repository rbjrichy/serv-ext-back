<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EsextInstitucionController;

Route::prefix('v1')->group(function () {
    Route::prefix('institucion/externa')->group(function () {
        // Operaciones de lectura
        Route::middleware(['permisos:servext-instituciones-externas-lectura'])->group(function () {
            Route::get('/', [EsextInstitucionController::class, 'index']);
            Route::get('/{instiExterna}', [EsextInstitucionController::class, 'show']);
        });

        // Operaciones de escritura
        Route::middleware(['permisos:servext-instituciones-externas-escritura'])->group(function () {
            Route::post('/', [EsextInstitucionController::class, 'store']);
            Route::put('/{instiExterna}', [EsextInstitucionController::class, 'update']);
            Route::patch('/{instiExterna}', [EsextInstitucionController::class, 'update']);
            Route::patch('/baja/{instiExterna}', [EsextInstitucionController::class, 'baja']);
            Route::patch('/reincorporar/{instiExterna}', [EsextInstitucionController::class, 'reincorporar']);
        });

        // Operaciones de eliminación
        Route::middleware(['permisos:servext-instituciones-externas-escritura'])->group(function () {
            Route::delete('/{instiExterna}', [EsextInstitucionController::class, 'destroy']);
        });
    });
});
