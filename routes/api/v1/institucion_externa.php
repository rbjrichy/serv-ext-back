<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\InstitucionExternaController;

// Route::prefix('v1')->middleware(['auth:api'])->group(function () {
//     Route::apiResource('/institucion/externa', InstitucionExternaController::class)->parameters(['externa' => 'instiExterna']);
// });

Route::prefix('v1')->middleware(['auth:api'])->group(function () {
    Route::prefix('institucion/externa')->group(function () {
        // Operaciones de lectura
        Route::middleware(['permisos:servext-instituciones-externas-lectura'])->group(function () {
            Route::get('/', [InstitucionExternaController::class, 'index']);
            Route::get('/{instiExterna}', [InstitucionExternaController::class, 'show']);
        });

        // Operaciones de escritura
        Route::middleware(['permisos:servext-instituciones-externas-escritura'])->group(function () {
            Route::post('/', [InstitucionExternaController::class, 'store']);
            Route::put('/{instiExterna}', [InstitucionExternaController::class, 'update']);
            Route::patch('/{instiExterna}', [InstitucionExternaController::class, 'update']);
        });

        // Operaciones de eliminación
        Route::middleware(['permisos:iservext-instituciones-externas-escritura'])->group(function () {
            Route::delete('/{instiExterna}', [InstitucionExternaController::class, 'destroy']);
        });
    });
});