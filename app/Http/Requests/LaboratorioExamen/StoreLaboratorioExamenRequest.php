<?php

namespace App\Http\Requests\LaboratorioExamen;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaboratorioExamenRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'unidad' => 'required|string|max:255',
            'referencia' => 'nullable|string',
            'costo' => 'required|numeric|min:0',
            'orden_lista' => 'required|integer|min:1',
            'laboratorio_categoria_id' => 'required|integer|min:1',
            'laboratorio_subcategoria_id' => 'required|integer|min:1',

        ];
    }
}
