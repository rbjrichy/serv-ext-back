<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Afiliado extends Model
{

    public static function buscar($request)
    {
        $codigo = $request->input('codigo');
        $nombres = $request->input('nombres');
        $entidad_id = Auth::getPayload()->get('entidad_id');
        $query =
            DB::connection('afiliacion')->table('afiliados as afi')
            ->join(DB::raw('persona_afiliado_entidads as pae FORCE INDEX (persona_afiliado_entidads_afiliado_id_foreign)'), 'pae.afiliado_id', '=', 'afi.id')
            ->join('personas as per', 'per.id', '=', 'pae.persona_id')
            ->join('estados as est', 'est.id', '=', 'afi.estado_id')
            ->join('entidads as ent', 'afi.entidad_id', '=', 'ent.id')
            ->leftJoin('users as usr', 'usr.id', '=', 'afi.usr')
            ->leftJoin(DB::raw('contratos as con FORCE INDEX (PRIMARY)'), 'con.id', '=', 'afi.ultimo_contrato_id')
            ->leftJoin('afiliado_tipos as afit', 'afit.id', '=', 'con.afiliado_tipo_id')
            ->leftJoin('baja_tipos as bt', 'bt.id', '=', 'con.baja_tipo_id')
            ->leftJoin('parentescos as par', 'par.id', '=', 'con.parentesco_id')
            ->select(
                'pae.id as pae_id',
                'pae.entidad_id as entidad_id',
                'con.codigo_referencia as codigo_referencia',
                'con.codigo_alternativo as codigo_alternativo',
                'con.fecha_inicio as fecha_inicio',
                'con.fecha_fin as fecha_fin',
                'con.fecha_cesantia as fecha_cesantia',
                'con.id as ultimo_contrato_id',
                'afit.tipo_afiliado as tipo_afiliado',
                'afi.matricula as matricula',
                'afi.fecha_nac as fecha_nac',
                'afi.fhr as fecha_mod',
                'per.apellido_paterno as paterno',
                'per.apellido_materno as materno',
                'per.nombre as nombre',
                //DB::raw("CONCAT(per.apellido_paterno,' ',per.apellido_materno,' ',per.nombre) AS nombre_completo"),
                'per.numero_identificacion as numero_identificacion',
                'per.complemento as complemento',
                'per.direccion_domicilio as direccion_domicilio',
                'per.direccion_trabajo as direccion_trabajo',
                'per.telefono_domicilio as tel_dom',
                'est.id as estado_id',
                'est.descripcion as estado',
                'bt.tipo_baja as tipo_baja',
                'usr.name as usuario',
                'par.parentesco as parentesco',
                'par.id as parentesco_id',
                'ent.nombre as entidad',
            );
        if ($entidad_id !== null) {
            $query = $query->where('pae.entidad_id', $entidad_id);
        }
        if ($codigo != null) {
            $query = $query->where(function ($q) use ($codigo) {
                $q->where('afi.matricula', 'LIKE', $codigo . '%')
                    ->orWhere('per.numero_identificacion', 'LIKE', $codigo . '%')
                    ->orWhere('con.codigo_referencia', 'LIKE', '%' . $codigo . '%');
            });
        }
        if ($nombres != null) {
            $query = $query->where(DB::raw("CONCAT(per.apellido_paterno,' ',per.apellido_materno,' ',per.nombre)"), 'LIKE', '%' . $nombres . '%');
        }
        $query->orderBy('con.afiliado_tipo_id');

        return $query->get();
    }

    public static function datos_paciente($id)
    {
        $query =
            DB::connection('afiliacion')->table('afiliados as afi')
            ->join('persona_afiliado_entidads as pae', 'pae.afiliado_id', '=', 'afi.id')
            ->join('personas as per', 'per.id', '=', 'pae.persona_id')
            ->join('generos as gen', 'gen.id', '=', 'per.genero_id')
            ->join('grupo_sanguineos as gsan', 'gsan.id', '=', 'afi.grupo_sanguineo_id')
            ->join('estados as est', 'est.id', '=', 'afi.estado_id')
            ->join('entidads as ent', 'afi.entidad_id', '=', 'ent.id')
            ->leftJoin('contratos as con', 'con.id', '=', 'afi.ultimo_contrato_id')
            ->leftJoin('institucions as ins', 'ins.id', '=', 'con.institucion_id')
            ->leftJoin('afiliado_tipos as afit', 'afit.id', '=', 'con.afiliado_tipo_id')
            ->select(
                'pae.id as pae_id',
                'pae.entidad_id as entidad_id',
                'con.codigo_referencia as codigo_referencia',
                'con.codigo_alternativo as codigo_alternativo',
                'con.id as ultimo_contrato_id',
                'afit.tipo_afiliado as tipo_afiliado',
                'afi.matricula as matricula',
                'afi.fecha_nac as fecha_nac',
                'per.apellido_paterno as paterno',
                'per.apellido_materno as materno',
                'per.nombre as nombre',
                'per.numero_identificacion as numero_identificacion',
                'per.complemento as complemento',
                'per.direccion_domicilio as direccion_domicilio',
                'per.telefono_domicilio as tel_dom',
                'est.id as estado_id',
                'est.descripcion as estado',
                'ent.nombre as entidad',
                'gen.genero as genero',
                'gsan.grupo_sanguineo as grupo_sanguineo',
                'ins.nombre as institucion'
            )
            ->where('pae.id', $id);
        return $query->first();
    }

    public static function instituciones()
    {
        $entidad_id = Auth::getPayload()->get('entidad_id');
        $query = DB::connection('afiliacion')->table('institucions as ins')
            ->select([
                'id',
                'nombre',
                'activo'
            ]);
        if (isset($entidad_id)) {
            $query = $query->where('entidad_id', $entidad_id);
        }
        $query = $query->orderBy('nombre', 'asc');
        return $query->get();
    }
}
