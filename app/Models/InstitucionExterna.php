<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstitucionExterna extends Model
{
    use SoftDeletes;

    protected $table = 'institucion_externa';

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion',
        'observaciones',
        'observaciones',
        'observaciones_baja',
        'observaciones_reincorporar',
        'estado_id',
        'usr_alta',
        'usr_mod',
    ];

    /**
     * Scope para obtener solo registros habilitados.
     */
    // public function scopeEnabled($query)
    // {
    //     return $query->where('estado', 'Habilitado');
    // }

    /**
     * Relación: Institución -> inst_ext_serv (uno a muchos)
     */
    public function instExtServ()
    {
        return $this->hasMany(InstExtServ::class, 'institucion_ext_id', 'id');
    }
}
