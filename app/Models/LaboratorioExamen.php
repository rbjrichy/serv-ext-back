<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class LaboratorioExamen extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'nombre',
        'unidad',
        'referencia',
        'costo',
        'orden_lista',
        'laboratorio_categoria_id',
        'laboratorio_subcategoria_id',
        'estado_id',
        'usr_alta',
        'usr_mod',
        'observaciones_baja',
        'observaciones_reincorporar'
    ];

    public static function buscar($request)
    {
        $paginado = $request->input('paginado');
        $page = $request->input('page', 1); // Página actual (por defecto: 1)
        $perPage = $request->input('per_page', 10); // Registros por página (por defecto: 10)
        $nombre = $request->input('nombre');
        $categoria_id = $request->input('categoria_id');
        $subcategoria_id = $request->input('subcategoria_id');
        $eliminado = $request->input('eliminado');
        $query = LaboratorioExamen::query();
        if (!empty($nombre)) {
            $query->where('nombre', 'like', '%' . $nombre . '%');
        }

        if (!empty($categoria_id)) {
            $query->where('laboratorio_categoria_id', $categoria_id);
        }
        if (!empty($subcategoria_id)) {
            $query->where('laboratorio_subcategoria_id', $subcategoria_id);
        }

        if (!is_null($eliminado) && $eliminado == 'false') {
            $query->where('estado_id', 17); // VISIBLE
        }
        $query = $query->orderBy('orden_lista', 'asc');
        return  $paginado == 'true' ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();
    }

    public static function examenes_subcategoria($subcategorias)
    {
        return LaboratorioExamen::where('estado_id', 17)
            ->whereIn('laboratorio_subcategoria_id', $subcategorias)
            ->select([
                'id'
            ])
            ->get()
            ->pluck('id')
            ->toArray();
    }

    public static function examenes_perfil($perfil_id)
    {
        return DB::table('laboratorio_examens as exam')
            // ->join('laboratorio_subcategorias as sub', 'sub.id', '=', 'exam.laboratorio_subcategoria_id')
            ->join('laboratorio_perfil_examens as lpe', 'lpe.laboratorio_subcategoria_id', '=', 'exam.laboratorio_subcategoria_id')
            ->join('laboratorio_perfils as per', 'per.id', '=', 'lpe.laboratorio_perfil_id')
            ->where('per.estado_id', 17)
            // ->where('sub.estado_id', 17)
            ->where('exam.estado_id', 17)
            ->where('per.id', $perfil_id)
            ->select([
                'exam.id as examen_id'
            ])
            ->get()
            ->pluck('examen_id')
            ->toArray();
    }

    public function solicitudes()
    {

        return $this->belongsToMany(
            LaboratorioSolicitud::class,
            'conex_laboratorio_examens', // Nombre de la tabla pivote
            'laboratorio_examen_id', // Clave foránea del modelo actual (LaboratorioSubcategoria) en la pivote
            'laboratorio_solicitud_id'       // Clave foránea del modelo relacionado (LaboratorioPerfil) en la pivote
        )->withTimestamps();
    }
}
