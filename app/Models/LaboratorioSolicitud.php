<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class LaboratorioSolicitud extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'telefono_referencia',
        'observaciones',
        'urgente',
        'fecha_solicitud',
        'fecha_entrega',
        'fecha_toma_muestra',
        'diagnosticos',
        'modulo_id',
        'entidad_id',
        'contrato_id',
        'paciente_id',
        'medicoespecialidad_id',
        'clasificacion_serv_id',
        'laboratorio_categoria_id',
        'laboratorio_subcategoria_id',
        'estado_id',
        'usr_alta',
        'usr_mod',
        'observaciones_anulado',
        'observaciones_cambioestado'
    ];

    public static function buscar($request)
    {
        $bd_afiliacion = config('database.connections.afiliacion.database');
        $entidad_id = Auth::payload()->get('entidad_id');
        $paginado = $request->input('paginado');
        $page = $request->input('page', 1); // Página actual (por defecto: 1)
        $perPage = $request->input('per_page', 10); // Registros por página (por defecto: 10)
        $nombres = $request->input('nombres');
        $codigo = $request->input('codigo');
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $estado_id = $request->input('estado_id');
        $urgente = $request->input('urgente');

        $query = DB::table('laboratorio_solicituds as lsol')
            ->join('estado_ocurrencias as est', 'est.id', '=', 'lsol.estado_id')
            ->join('modulos as mod', 'mod.id', '=', 'lsol.modulo_id')
            ->join('clasificacion_servicios as cser', 'cser.id', '=', 'lsol.clasificacion_serv_id')
            ->join(DB::raw($bd_afiliacion . '.persona_afiliado_entidads as pae'), 'pae.id', '=', 'lsol.paciente_id')
            ->join(DB::raw($bd_afiliacion . '.fi_med_esps as fmes'), 'fmes.id', '=', 'lsol.medicoespecialidad_id')
            ->join(DB::raw($bd_afiliacion . '.fi_medicos as fmed'), 'fmed.id', '=', 'fmes.medico_id')
            ->join(DB::raw($bd_afiliacion . '.fi_subespecialidads as fsub'), 'fsub.id', '=', 'fmes.subespecialidad_id')
            ->join(DB::raw($bd_afiliacion . '.personas as perm'), 'perm.id', '=', 'fmed.persona_id')
            ->join(DB::raw($bd_afiliacion . '.personas as per'), 'per.id', '=', 'pae.persona_id')
            ->join(DB::raw($bd_afiliacion . '.afiliados as afi'), 'afi.id', '=', 'pae.afiliado_id')
            ->leftJoin(DB::raw($bd_afiliacion . '.contratos as con'), 'con.id', '=', 'afi.ultimo_contrato_id')
            ->leftJoin(DB::raw($bd_afiliacion . '.institucions as ins'), 'ins.id', '=', 'con.institucion_id')
            ->select([
                'lsol.id as id',
                'lsol.paciente_id as paciente_id',
                'lsol.contrato_id as contrato_id',
                'lsol.estado_id as estado_id',
                'lsol.usr_alta as usr_alta',
                'lsol.usr_mod as usr_mod',
                'lsol.fecha_solicitud as fecha_solicitud',
                'lsol.fecha_toma_muestra as fecha_toma_muestra',
                'lsol.fecha_entrega as fecha_entrega',
                'lsol.modulo_id as modulo_id',
                'lsol.urgente as urgente',
                'pae.entidad_id as entidad_id',
                'con.codigo_referencia as codigo_referencia',
                'con.codigo_alternativo as codigo_alternativo',
                'con.id as ultimo_contrato_id',
                'afi.matricula as matricula',
                'afi.fecha_nac as fecha_nac',
                'per.apellido_paterno as paterno',
                'per.apellido_materno as materno',
                'per.nombre as nombre',
                'perm.apellido_paterno as paterno_medico',
                'perm.apellido_materno as materno_medico',
                'perm.nombre as nombre_medico',
                'per.direccion_domicilio as direccion_domicilio',
                'per.telefono_domicilio as tel_dom',
                'est.descripcion as estado',
                'mod.descripcion as modulo',
                'fsub.especialidad as especialidad',
                'cser.descripcion as clasificacion_servicio',
                'ins.nombre as institucion',
                DB::raw('(SELECT COUNT(r.id) FROM conex_laboratorio_examens as r WHERE r.laboratorio_solicitud_id = lsol.id) as numero_examenes'),
                DB::raw('(SELECT COUNT(r.id) FROM conex_laboratorio_examens as r WHERE r.laboratorio_solicitud_id = lsol.id AND r.resultado IS NOT NULL) as numero_llenados')
            ]);
        if (!is_null($entidad_id)) {
            $query->where('lsol.entidad_id', $entidad_id);
        }
        if (!is_null($estado_id)) {
            $query->where('lsol.estado_id', $estado_id);
        }

        if (!is_null($fecha_inicio) && !is_null($fecha_fin)) {
            $query->where('lsol.fecha_solicitud', '>=', $fecha_inicio)
                ->where('lsol.fecha_solicitud', '<=', $fecha_fin);
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
        if (!is_null($urgente) && $urgente == 'true') {
            $query = $query->orderBy('lsol.urgente', 'desc');
        }
        $query = $query->orderBy('lsol.created_at', 'desc');
        return  $paginado == 'true' ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();
    }

    public static function datos_solicitud($id)
    {
        $bd_afiliacion = config('database.connections.afiliacion.database');
        $query = DB::table('laboratorio_solicituds as lsol')
            ->join(DB::raw($bd_afiliacion . '.fi_med_esps as fmes'), 'fmes.id', '=', 'lsol.medicoespecialidad_id')
            ->join(DB::raw($bd_afiliacion . '.fi_medicos as fmed'), 'fmed.id', '=', 'fmes.medico_id')
            ->join(DB::raw($bd_afiliacion . '.fi_subespecialidads as fsub'), 'fsub.id', '=', 'fmes.subespecialidad_id')
            ->join(DB::raw($bd_afiliacion . '.personas as perm'), 'perm.id', '=', 'fmed.persona_id')
            ->select([
                'lsol.id as id',
                'lsol.paciente_id as paciente_id',
                'lsol.fecha_solicitud as fecha_solicitud',
                'lsol.fecha_toma_muestra as fecha_toma_muestra',
                'lsol.fecha_entrega as fecha_entrega',
                'lsol.observaciones_anulado as observaciones_anulado',
                'lsol.observaciones_cambioestado as observaciones_cambioestado',
                'lsol.diagnosticos as diagnostico',
                'lsol.estado_id as estado_id',
                'lsol.observaciones as observaciones',
                'lsol.urgente as urgente',
                'perm.apellido_paterno as paterno_medico',
                'perm.apellido_materno as materno_medico',
                'perm.nombre as nombre_medico',
                'fsub.especialidad as especialidad',
            ])
            ->where('lsol.id', $id);

        return $query->first();
    }

    public static function get_examenes_resultados($solicitud_id)
    {
        return DB::table('conex_laboratorio_examens as cle')
            ->join('laboratorio_examens as lex', 'lex.id', '=', 'cle.laboratorio_examen_id')
            ->join('laboratorio_categorias as lcat', 'lcat.id', '=', 'lex.laboratorio_categoria_id')
            ->join('laboratorio_subcategorias as lsub', 'lsub.id', '=', 'lex.laboratorio_subcategoria_id')
            ->where('cle.laboratorio_solicitud_id', $solicitud_id)
            ->select([
                'cle.id as laboratorio_examen_id',
                'cle.resultado as resultado',
                'lex.unidad as unidad',
                'lex.referencia as referencia',
                'lex.nombre as nombre_examen',
                'lex.orden_lista as orden_lista',
                'lcat.id as categoria_id',
                'lcat.nombre as categoria',
                'lsub.id as subcategoria_id',
                'lsub.nombre as subcategoria',
            ])
            ->orderBy('lcat.nombre', 'asc')
            ->orderBy('lsub.nombre', 'asc')
            ->orderBy('lex.orden_lista', 'asc')
            ->get();
    }

    public function examenes_solicitud()
    {

        return $this->belongsToMany(
            LaboratorioSolicitud::class,
            'conex_laboratorio_examens', // Nombre de la tabla pivote
            'laboratorio_solicitud_id',       // Clave foránea del modelo relacionado (LaboratorioPerfil) en la pivote
            'laboratorio_examen_id' // Clave foránea del modelo actual (LaboratorioSubcategoria) en la pivote
        )->withTimestamps();
    }
}
