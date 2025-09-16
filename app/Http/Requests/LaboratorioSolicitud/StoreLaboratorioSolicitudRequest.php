<?php

namespace App\Http\Requests\LaboratorioSolicitud;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaboratorioSolicitudRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'observaciones' => 'nullable|string',
            'urgente' => 'required|boolean',
            'fecha_solicitud' => 'required|date',
            'diagnosticos' => 'required|string',
            'entidad_id' => 'required|integer|min:1',
            'contrato_id' => 'required|integer|min:1',
            'paciente_id'=> 'required|integer|min:1',
            'medicoespecialidad_id' => 'required|integer|min:1',
            'clasificacion_serv_id' => 'required|integer|min:1',
            'subcategorias' => 'nullable|array',
            'tipo_solicitud' => 'required|string|in:examen,perfil' ,
            'perfil_id' => 'nullable|integer',
            'conex_id' => 'nullable|integer'
        ];
    }
}
