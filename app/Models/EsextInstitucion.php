<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsextInstitucion extends Model
{
    protected $table = 'esext_instituciones';

    protected $fillable = [
        'institucion',
        'observaciones',
        'telefono',
        'direccion',
        'estado_id',
        'usr_alta',
        'usr_mod',
        'fecha_registro',
        'observaciones_baja',
        'observaciones_reincorporar',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Relación con estado_ocurrencias
     */
    public function estado()
    {
        return $this->belongsTo(EstadoOcurrencias::class, 'estado_id');
    }
    public function serviciosInstitucion()
    {
        return $this->hasMany(EsextServicioInstitucion::class, 'esext_institucion_id');
    }

}
