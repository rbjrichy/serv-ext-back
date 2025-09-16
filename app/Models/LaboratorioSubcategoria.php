<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class LaboratorioSubcategoria extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'nombre',
        'estado_id',
        'usr_alta',
        'usr_mod',
        'examen_externo',
        'laboratorio_categoria_id',
        'tiempo_procesamiento',
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
        $eliminado = $request->input('eliminado');
        $query = LaboratorioSubcategoria::query();
        if (!empty($nombre)) {
            $query->where('nombre', 'like', '%' . $nombre . '%');
        }
        if (!empty($categoria_id)) {
            $query->where('laboratorio_categoria_id', $categoria_id);
        }

        if (!is_null($eliminado) && $eliminado == 'false') {
            $query->where('estado_id', 17); // VISIBLE
        }
        $query = $query->orderBy('nombre', 'asc');
        return  $paginado == 'true' ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();
    }

    public function examenes()
    {
        return $this->hasMany(LaboratorioExamen::class);
    }

    public function subcategoria()
    {
        return $this->belongsTo(LaboratorioCategoria::class);
    }

    public function examenes_solicitud()
    {

        return $this->belongsToMany(
            LaboratorioExamen::class,
            'conex_laboratorio_examens',
            'laboratorio_solicitud_id',
            'laboratorio_examen_id'
        );
    }
}
