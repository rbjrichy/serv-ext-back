<?php

use App\Http\Controllers\AfiliadoController;
use App\Http\Controllers\ClasificacionServicioController;
use App\Http\Controllers\FiMedEspController;
use App\Http\Controllers\LaboratorioCategoriaController;
use App\Http\Controllers\LaboratorioExamenController;
use App\Http\Controllers\LaboratorioPerfilController;
use App\Http\Controllers\LaboratorioSolicitudController;
use App\Http\Controllers\LaboratorioSubcategoriaController;
use App\Http\Controllers\ReporteLaboratorioController;
use Illuminate\Support\Facades\Route;




//AFILIADOS
Route::prefix('afiliado')->group(function () {
    Route::get('/', [AfiliadoController::class, 'index'])->middleware('permisos:afiliados-lectura');
    Route::get('/{id}', [AfiliadoController::class, 'datos_paciente'])->middleware('permisos:afiliados-lectura');
});

//INSTITUCION
Route::prefix('institucion')->group(function () {
    Route::get('/', [AfiliadoController::class, 'instituciones'])->middleware('permisos:afiliados-lectura');
});

//ESPECIADLIDADES MEDICOS
Route::prefix('medico-especialidad')->group(function () {
    Route::get('/', [FiMedEspController::class, 'index'])->middleware('permisos:afiliados-lectura');
});

//CLASIFICACION SERVICIO
Route::prefix('clasificacion-servicio')->group(function () {
    Route::get('/', [ClasificacionServicioController::class, 'index'])->middleware('permisos:afiliados-lectura');
});

//SOLICITUDES
Route::prefix('solicitud')->group(function () {
    Route::get('/', [LaboratorioSolicitudController::class, 'index'])->middleware('permisos:solicitudes-laboratorio-lectura');
    Route::get('/lista-examenes/{id}', [LaboratorioSolicitudController::class, 'get_examenes_resultados'])->middleware('permisos:solicitudes-laboratorio-lectura');
    Route::get('/{id}', [LaboratorioSolicitudController::class, 'datos_solicitud'])->middleware('permisos:solicitudes-laboratorio-lectura');
    Route::post('/', [LaboratorioSolicitudController::class, 'store'])->middleware('permisos:solicitudes-laboratorio-escritura');
    Route::patch('/cambio-estado/{id}', [LaboratorioSolicitudController::class, 'cambio_estado'])->middleware('permisos:solicitudes-laboratorio-anular');
    Route::patch('/programar/{id}', [LaboratorioSolicitudController::class, 'programar'])->middleware('permisos:solicitudes-laboratorio-escritura');
    Route::patch('/llenado/{id}', [LaboratorioSolicitudController::class, 'llenado'])->middleware('permisos:solicitudes-laboratorio-escritura');
    // Route::patch('/{id}/generar-informe', [FisioterapiaSolicitudController::class, 'generar_informe'])->middleware('permisos:solicitudes-escritura');
});

//LABORATORIO CATEGORIA
Route::prefix('laboratorio-categoria')->group(function () {
    Route::get('/', [LaboratorioCategoriaController::class, 'index'])->middleware('permisos:laboratorio-categorias-lectura');
    Route::get('/listar-subcategorias', [LaboratorioCategoriaController::class, 'listar_subcategorias'])->middleware('permisos:laboratorio-categorias-lectura');
    Route::get('/{id}', [LaboratorioCategoriaController::class, 'get_one'])->middleware('permisos:laboratorio-categorias-lectura');
    Route::post('/', [LaboratorioCategoriaController::class, 'store'])->middleware('permisos:laboratorio-categorias-escritura');
    Route::patch('/{id}', [LaboratorioCategoriaController::class, 'update'])->middleware('permisos:laboratorio-categorias-escritura');
    Route::patch('/{id}/baja', [LaboratorioCategoriaController::class, 'baja'])->middleware('permisos:laboratorio-categorias-escritura');
    Route::patch('/{id}/reincorporar', [LaboratorioCategoriaController::class, 'reincorporar'])->middleware('permisos:laboratorio-categorias-escritura');
});

//LABORATORIO SUBCATEGORIA
Route::prefix('laboratorio-subcategoria')->group(function () {
    Route::get('/', [LaboratorioSubcategoriaController::class, 'index'])->middleware('permisos:laboratorio-subcategorias-lectura');
    Route::get('/{id}', [LaboratorioSubcategoriaController::class, 'get_one'])->middleware('permisos:laboratorio-subcategorias-lectura');
    Route::post('/', [LaboratorioSubcategoriaController::class, 'store'])->middleware('permisos:laboratorio-subcategorias-escritura');
    Route::patch('/{id}', [LaboratorioSubcategoriaController::class, 'update'])->middleware('permisos:laboratorio-subcategorias-escritura');
    Route::patch('/{id}/baja', [LaboratorioSubcategoriaController::class, 'baja'])->middleware('permisos:laboratorio-subcategorias-escritura');
    Route::patch('/{id}/reincorporar', [LaboratorioSubcategoriaController::class, 'reincorporar'])->middleware('permisos:laboratorio-subcategorias-escritura');
});

//LABORATORIO EXAMEN
Route::prefix('laboratorio-examen')->group(function () {
    Route::get('/', [LaboratorioExamenController::class, 'index'])->middleware('permisos:laboratorio-examenes-lectura');
    Route::post('/', [LaboratorioExamenController::class, 'store'])->middleware('permisos:laboratorio-examenes-escritura');
    Route::post('/maximo-orden-lista', [LaboratorioExamenController::class, 'maximo_orden_lista'])->middleware('permisos:laboratorio-examenes-lectura');
    Route::patch('/{id}', [LaboratorioExamenController::class, 'update'])->middleware('permisos:laboratorio-examenes-escritura');
    Route::patch('/{id}/baja', [LaboratorioExamenController::class, 'baja'])->middleware('permisos:laboratorio-examenes-escritura');
    Route::patch('/{id}/reincorporar', [LaboratorioExamenController::class, 'reincorporar'])->middleware('permisos:laboratorio-examenes-escritura');
});


//LABORATORIO PERFILES
Route::prefix('laboratorio-perfil')->group(function () {
    Route::get('/', [LaboratorioPerfilController::class, 'index'])->middleware('permisos:laboratorio-perfiles-lectura');
    Route::get('/{id}', [LaboratorioPerfilController::class, 'get_one'])->middleware('permisos:laboratorio-perfiles-lectura');
    Route::post('/', [LaboratorioPerfilController::class, 'store'])->middleware('permisos:laboratorio-perfiles-escritura');
    Route::patch('/{id}', [LaboratorioPerfilController::class, 'update'])->middleware('permisos:laboratorio-perfiles-escritura');
    Route::patch('/{id}/baja', [LaboratorioPerfilController::class, 'baja'])->middleware('permisos:laboratorio-perfiles-escritura');
    Route::patch('/{id}/reincorporar', [LaboratorioPerfilController::class, 'reincorporar'])->middleware('permisos:laboratorio-perfiles-escritura');
});

//REPORTE
Route::prefix('reporte')->group(function () {
    Route::get('/solicitud-laboratorio/{id}', [ReporteLaboratorioController::class, 'generar_solicitud'])->middleware('permisos:reportes-laboratorio');
    Route::get('/informe-laboratorio/{id}', [ReporteLaboratorioController::class, 'generar_informe'])->middleware('permisos:reportes-laboratorio');

});

