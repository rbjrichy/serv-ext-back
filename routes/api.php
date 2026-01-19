<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {
    require __DIR__ . '/api/v1/esext_institucion.php';
    require __DIR__ . '/api/v1/esext_servicio.php';
    require __DIR__ . '/api/v1/esext_institucion_servicio.php';
    require __DIR__ . '/api/v1/afiliado.php';
    require __DIR__ . '/api/v1/esext_solicitud_venta_servicio.php';
});
