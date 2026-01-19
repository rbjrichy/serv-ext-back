<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class EsextServicio extends Model
{
    protected $table = 'esext_servicios';

    protected $fillable = [
        'servicio',
        'observaciones',
        'observaciones_baja',
        'observaciones_reincorporar',
        'usr_alta',
        'usr_mod',
        'estado_id',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Relación con estado_ocurrencias
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoOcurrencias::class, 'estado_id');
    }
    // 🔗 Relación inversa con servicios_instituciones
    public function instituciones()
    {
        return $this->hasMany(EsextServicioInstitucion::class, 'esext_servicio_id');
    }

}
