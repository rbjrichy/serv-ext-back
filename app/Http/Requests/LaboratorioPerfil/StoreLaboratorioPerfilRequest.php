<?php

namespace App\Http\Requests\LaboratorioPerfil;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLaboratorioPerfilRequest extends FormRequest
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
            'subcategorias' => 'nullable|array',
            'subcategorias.*' => ['integer', Rule::exists('laboratorio_subcategorias', 'id')]
        ];
    }
}
