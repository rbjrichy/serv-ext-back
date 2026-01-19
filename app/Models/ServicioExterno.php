<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicioExterno extends Model
{
    use SoftDeletes;

    protected $table = 'servicio_externo';

    protected $fillable = [
        'nombre_servicio',
        'tipo_servicio_id',
        'observaciones',
        'estado',
        'usuario_creacion_id',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('estado', 'Habilitado');
    }

    /**
     * Relación: Servicio -> inst_ext_serv (uno a muchos)
     */
    public function instExtServ()
    {
        return $this->hasMany(InstExtServ::class, 'servicio_id', 'id');
    }
}
