<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class LaboratorioPerfilExamen extends Model implements Auditable

{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'laboratorio_perfil_examens';

    protected $fillable = [
        'laboratorio_perfil_id',
        'laboratorio_subcategoria_id',
    ];

}
