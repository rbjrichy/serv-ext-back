<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EsextSolicitudVentaServicio extends Model
{
    protected $table = 'esext_solicitudventaservicios';

    protected $fillable = [
        'fecha_solicitud',
        'entidad_id',
        'titular_persona_id',
        'paciente_persona_id',
        'contrato_id',
        'fi_med_esps_id',
        'clasificacion_servicio_id',
        'esext_servicio_institucion_id',
        'observaciones',
        'estado_id',
        'fecha_registro',
        'usuario',
        'solicitante',
        'cargo',
        'diagnostico',
        'fecha_modificado',
        'usr_mod',
        'justificacion_mod',
        'consulta_externa_id',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_registro' => 'datetime',
        'fecha_modificado' => 'datetime',
    ];

    // Relaciones

    public function entidad(): BelongsTo
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoOcurrencias::class, 'estado_id');
    }

    public function clasificacionServicio(): BelongsTo
    {
        return $this->belongsTo(ClasificacionServicio::class, 'clasificacion_servicio_id');
    }

    public function servicioInstitucion(): BelongsTo
    {
        return $this->belongsTo(EsextServicioInstitucion::class, 'esext_servicio_institucion_id');
    }

    public function consultaExterna(): BelongsTo
    {
        return $this->belongsTo(ConsultaExterna::class, 'consulta_externa_id');
    }

    // Relaciones opcionales (no definidas como FK pero útiles si existen modelos)

    // public function titular(): BelongsTo
    // {
    //     return $this->belongsTo(Persona::class, 'titular_persona_id');
    // }

    // public function paciente(): BelongsTo
    // {
    //     return $this->belongsTo(Persona::class, 'paciente_persona_id');
    // }

    // public function contrato(): BelongsTo
    // {
    //     return $this->belongsTo(Contrato::class, 'contrato_id');
    // }

    // public function fiMedEspecialidad(): BelongsTo
    // {
    //     return $this->belongsTo(FiMedEspecialidad::class, 'fi_med_esps_id');
    // }

}
