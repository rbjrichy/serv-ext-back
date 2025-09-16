<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class LaboratorioPerfil extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'laboratorio_perfils';
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
        $query = LaboratorioPerfil::query()->with('perfil_examenes');
        if (!empty($nombre)) {
            $query->where('nombre', 'like', '%' . $nombre . '%');
        }

        if (!is_null($eliminado) && $eliminado == 'false') {
            $query->where('estado_id', 17); // VISIBLE
        }
        return  $paginado == 'true' ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();
    }

    public function perfil_examenes()
    {
        return $this->belongsToMany(
            LaboratorioSubcategoria::class,
            'laboratorio_perfil_examens', // Nombre de la tabla pivote
            'laboratorio_perfil_id',      // Clave foránea del modelo actual (LaboratorioPerfil) en la pivote
            'laboratorio_subcategoria_id' // Clave foránea del modelo relacionado (LaboratorioSubcategoria) en la pivote
        );
    }
}
