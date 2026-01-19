<?php

use App\Http\Controllers\Api\V1\AfiliadoController;
use App\Http\Controllers\Api\V1\FiMedEspController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    // Operaciones de lectura
    Route::middleware(['permisos:servext-afiliados-lectura'])->prefix('/afiliado/buscar')->group(function () {
        Route::post('/', [AfiliadoController::class, 'buscar_afiliado']);
        Route::get('/{id}', [AfiliadoController::class, 'datos_paciente']);
    });
});


//ESPECIADLIDADES MEDICOS
Route::prefix('/v1')->group(function () {
    // Operaciones de lectura
    Route::middleware(['permisos:servext-afiliados-lectura'])->prefix('/medico-especialidad')->group(function () {
        Route::get('/', [FiMedEspController::class, 'index'])->middleware('permisos:servext-afiliados-lectura');
    });
});
