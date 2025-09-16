<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class LaboratorioCategoria extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'nombre',
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
        $eliminado = $request->input('eliminado');
        $query = LaboratorioCategoria::query();
        if (!empty($nombre)) {
            $query->where('nombre', 'like', '%' . $nombre . '%');
        }

        if (!is_null($eliminado) && $eliminado == 'false') {
            $query->where('estado_id', 17); // VISIBLE
        }
        $query = $query->orderBy('nombre', 'asc');
        return  $paginado == 'true' ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();
    }

    public static function listar_subcategorias($request)
    {
        $eliminado = $request->input('eliminado');

        $query = LaboratorioCategoria::query();

        if (!is_null($eliminado) && $eliminado == 'false') {
            $query->where('estado_id', 17);
        }


        return $query->with(['subcategorias' => function ($query_subcategoria) use ($eliminado) {
            $query_subcategoria->whereHas('examenes', function ($examQuery) use ($eliminado) {
                if (!is_null($eliminado) && $eliminado == 'false') {
                    $examQuery->where('estado_id', 17);
                }
            });

            if (!is_null($eliminado) && $eliminado == 'false') {
                $query_subcategoria->where('estado_id', 17);
            }
            $query_subcategoria->orderBy('nombre', 'asc');
        }])
            ->orderBy('nombre', 'asc')
            ->get(); //
    }

    public function subcategorias()
    {
        return $this->hasMany(LaboratorioSubcategoria::class);
    }
}
