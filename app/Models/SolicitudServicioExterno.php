<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudServicioExterno extends Model
{
    use SoftDeletes;

    protected $table = 'solicitud_servicio_externo';

    protected $fillable = [
        'fecha_solicitud',
        'paciente_id',
        'med_esp_id',
        'inst_ext_serv_id',
        'diagnostico',
        'entidad_id',
        'tipo_solicitud',
        'codigo_consulta',
        'usuario_creacion_id',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
    ];

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_solicitud', $tipo);
    }

    /**
     * Relación: Solicitud pertenece a InstExtServ
     */
    public function instExtServ()
    {
        return $this->belongsTo(InstExtServ::class, 'inst_ext_serv_id', 'id');
    }

}
