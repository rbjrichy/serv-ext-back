<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ConexLaboratorioExamen extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'resultado',
        'laboratorio_solicitud_id',
        'laboratorio_examen_id',
        'estado_id',
        'usr_alta',
        'usr_mod',
        'observaciones_baja',
    ];
}
