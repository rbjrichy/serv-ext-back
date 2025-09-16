<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FiMedEsp extends Model
{
    public static function buscar($request){
        $entidad_id = Auth::getPayload()->get('entidad_id');
        $hoy = now()->format('Y-m-d');
        $query = DB::connection('afiliacion')->table('fi_med_esps as fmes')
        ->join('fi_medicos as fmed','fmed.id' ,'=','fmes.medico_id')
        ->join('fi_subespecialidads as fsup','fsup.id' ,'=','fmes.subespecialidad_id')
        ->join('entidads as ent','ent.id' ,'=','fmes.entidad_id')
        ->join('personas as per','per.id' ,'=','fmed.persona_id')
        ->select([
            'fmes.id as id',
            'fsup.especialidad as especialidad',
            'per.apellido_paterno as paterno',
            'per.apellido_materno as materno',
            'per.nombre as nombres',
            'ent.nombre as entidad'
        ])
        ->where('fmes.estado_id',6) // ESPECIALIDADES HABILITADAS
        ->where('fsup.modalidad_id',1) // ESPECIALIDADES PRESENCIALES
        ->where('fmes.fecha_inicio', '<=', $hoy)
        ->where('fmes.fecha_fin', '>=', $hoy);
        if($entidad_id !== null){
            $query = $query->where('fmes.entidad_id',$entidad_id);
        }
        return $query->orderBy('fsup.especialidad')->get();
    }
}
