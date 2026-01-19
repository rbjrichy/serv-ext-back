<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EsextServicioInstitucion extends Model
{
    protected $table = 'esext_servicios_instituciones';

    protected $fillable = [
        'esext_institucion_id',
        'esext_servicio_id',
        'costo',
        "costo",
        "observaciones",
        "observaciones_baja",
        "observaciones_reincorporar",
        "usr_alta",
        "usr_mod",
        "fecha_inicio",
        "fecha_fin",
        "fecha_registro",
        "estado_id",
        "entidad_id",
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_registro' => 'datetime',
        'costo' => 'decimal:2',
    ];

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(EsextInstitucion::class, 'esext_institucion_id');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(EsextServicio::class, 'esext_servicio_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoOcurrencias::class, 'estado_id');
    }

    public function entidad(): BelongsTo
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }
}
