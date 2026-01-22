<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Afiliado extends Model
{
    protected $connection = 'afiliacion';
    protected $table = 'afiliados';

    public static function buscar($request)
    {
        $codigo   = $request->input('codigo');
        $ci       = $request->input('ci');
        $apellido1 = $request->input('apellido1');
        $apellido2 = $request->input('apellido2');
        $nombres   = $request->input('nombres');
        $entidad_id = Auth::getPayload()->get('entidad_id');
        $query = DB::connection('mysql_sistema_ssu')->table('afiliados as afi')
            ->join('persona_afiliado_entidads as pae', 'pae.afiliado_id', '=', 'afi.id')
            ->join('personas as per', 'per.id', '=', 'pae.persona_id')
            ->join('estados as est', 'est.id', '=', 'afi.estado_id')
            ->join('entidads as ent', 'afi.entidad_id', '=', 'ent.id')
            ->leftJoin('users as usr', 'usr.id', '=', 'afi.usr')
            ->leftJoin('contratos as con', 'con.id', '=', 'afi.ultimo_contrato_id')
            ->leftJoin('afiliado_tipos as afit', 'afit.id', '=', 'con.afiliado_tipo_id')
            ->leftJoin('baja_tipos as bt', 'bt.id', '=', 'con.baja_tipo_id')
            ->leftJoin('parentescos as par', 'par.id', '=', 'con.parentesco_id')
            ->select(
                'pae.id as pae_id',
                'pae.entidad_id',
                'con.codigo_referencia',
                'con.codigo_alternativo',
                'con.fecha_inicio',
                'con.fecha_fin',
                'con.fecha_cesantia',
                'con.id as ultimo_contrato_id',
                'afit.tipo_afiliado',
                'afi.matricula',
                'afi.fecha_nac',
                'afi.fhr as fecha_mod',
                'per.apellido_paterno as paterno',
                'per.apellido_materno as materno',
                'per.nombre',
                'per.numero_identificacion',
                'per.complemento',
                'per.direccion_domicilio',
                'per.direccion_trabajo',
                'per.telefono_domicilio as tel_dom',
                'est.id as estado_id',
                'est.descripcion as estado',
                'bt.tipo_baja',
                'usr.name as usuario',
                'par.parentesco',
                'par.id as parentesco_id',
                'ent.nombre as entidad'
            );

        // Filtros condicionales
        $query->when($entidad_id, fn($q) => $q->where('pae.entidad_id', $entidad_id));

        $query->when($codigo, fn($q) =>
            $q->where(function ($sub) use ($codigo) {
                $sub->where('afi.matricula', 'LIKE', $codigo.'%')
                    ->orWhere('per.numero_identificacion', 'LIKE', $codigo.'%')
                    ->orWhere('con.codigo_referencia', 'LIKE', '%'.$codigo.'%');
            })
        );

        $query->when($ci, fn($q) => $q->where('per.numero_identificacion', 'LIKE', $ci.'%'));
        $query->when($apellido1, fn($q) => $q->where('per.apellido_paterno', 'LIKE', '%'.$apellido1.'%'));
        $query->when($apellido2, fn($q) => $q->where('per.apellido_materno', 'LIKE', '%'.$apellido2.'%'));
        $query->when($nombres, fn($q) => $q->where('per.nombre', 'LIKE', '%'.$nombres.'%'));

        $query->orderBy('con.afiliado_tipo_id')->orderBy('per.apellido_paterno');

        return $query->get();
    }

    public static function datos_paciente($id)
    {
        return DB::connection('mysql_sistema_ssu')->table('afiliados as afi')
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
                'pae.entidad_id',
                'con.codigo_referencia',
                'con.codigo_alternativo',
                'con.id as ultimo_contrato_id',
                'con.cod_titular as titular_persona_id',
                'afit.tipo_afiliado',
                'afi.matricula',
                'afi.fecha_nac',
                'per.apellido_paterno as paterno',
                'per.apellido_materno as materno',
                'per.nombre',
                'per.numero_identificacion',
                'per.complemento',
                'per.direccion_domicilio',
                'per.telefono_domicilio as tel_dom',
                'est.id as estado_id',
                'est.descripcion as estado',
                'ent.nombre as entidad',
                'gen.genero',
                'gsan.grupo_sanguineo',
                'ins.nombre as institucion'
            )
            ->where('pae.id', $id)
            ->first();
    }

    public static function instituciones()
    {
        $entidad_id = Auth::getPayload()->get('entidad_id');

        $query = DB::connection('mysql_sistema_ssu')->table('institucions as ins')
            ->select('id','nombre','activo')
            ->orderBy('nombre','asc');

        if ($entidad_id) {
            $query->where('entidad_id', $entidad_id);
        }

        return $query->get();
    }
}
