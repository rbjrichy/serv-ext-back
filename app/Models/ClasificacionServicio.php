<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClasificacionServicio extends Model
{
    public static function buscar($request)
    {
        $paginado = $request->input('paginado');
        $page = $request->input('page', 1); // Página actual (por defecto: 1)
        $perPage = $request->input('per_page', 10); // Registros por página (por defecto: 10)
        $nombre = $request->input('descripcion');
        $eliminado = $request->input('eliminado');
        $query = ClasificacionServicio::query();
        if (!empty($nombre)) {
            $query->where('descripcion', 'like', '%' . $nombre . '%');
        }
        if (!is_null($eliminado)) {
            $query->where('estado_id', $eliminado == 'false' ? 17 : 3);
        }
        $query = $query->orderBy('descripcion', 'asc');
        return  $paginado == 'true' ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();
    }
}
