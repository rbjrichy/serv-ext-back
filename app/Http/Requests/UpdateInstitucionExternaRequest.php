<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstitucionExternaRequest extends FormRequest
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
    public function rules()
    {
        return [
            'nombre' => 'sometimes|required|string|max:255',
            'observaciones' => 'nullable|string',
            'estado' => 'nullable|string|in:Habilitado,Deshabilitado',
            'usuario_creacion_id' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio cuando se envía.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'estado.in' => 'El estado debe ser Habilitado o Deshabilitado.',
        ];
    }
}
