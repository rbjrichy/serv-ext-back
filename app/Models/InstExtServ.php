<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstExtServ extends Model
{
    use SoftDeletes;

    protected $table = 'inst_ext_serv';

    protected $fillable = [
        'institucion_ext_id',
        'servicio_id',
        'observaciones',
        'estado',
        'usuario_update_id',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('estado', 'Habilitado');
    }

    /**
     * Relación: pertenece a InstitucionExterna
     */
    public function institucion()
    {
        return $this->belongsTo(InstitucionExterna::class, 'institucion_ext_id', 'id');
    }

    /**
     * Relación: pertenece a ServicioExterno
     */
    public function servicio()
    {
        return $this->belongsTo(ServicioExterno::class, 'servicio_id', 'id');
    }

    /**
     * Relación: InstExtServ -> Solicitudes (uno a muchos)
     */
    public function solicitudes()
    {
        return $this->hasMany(SolicitudServicioExterno::class, 'inst_ext_serv_id', 'id');
    }
}
