<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstitucionExternaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //cambiar esto a false que que valide las aotorizaciones
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:1000',
            'observaciones_baja' => 'nullable|string|max:1000',
            'observaciones_reincorporar' => 'nullable|string|max:1000',
            'estado_id' => 'required|integer',
            'usr_alta' => 'nullable|string|max:50',
            'usr_mod' => 'nullable|string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre de la institución es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',

            'telefono.string' => 'El teléfono debe ser una cadena de texto.',
            'telefono.max' => 'El teléfono no debe exceder los 50 caracteres.',

            'direccion.string' => 'La dirección debe ser una cadena de texto.',
            'direccion.max' => 'La dirección no debe exceder los 255 caracteres.',

            'observaciones.string' => 'Las observaciones deben ser una cadena de texto.',
            'observaciones.max' => 'Las observaciones no deben exceder los 1000 caracteres.',

            'observaciones_baja.string' => 'Las observaciones de baja deben ser una cadena de texto.',
            'observaciones_baja.max' => 'Las observaciones de baja no deben exceder los 1000 caracteres.',

            'observaciones_reincorporar.string' => 'Las observaciones de reincorporación deben ser una cadena de texto.',
            'observaciones_reincorporar.max' => 'Las observaciones de reincorporación no deben exceder los 1000 caracteres.',

            'estado_id.required' => 'El estado es obligatorio.',
            'estado_id.integer' => 'El estado debe ser un número entero.',

            'usr_alta.string' => 'El usuario de alta debe ser una cadena de texto.',
            'usr_alta.max' => 'El usuario de alta no debe exceder los 50 caracteres.',

            'usr_mod.string' => 'El usuario de modificación debe ser una cadena de texto.',
            'usr_mod.max' => 'El usuario de modificación no debe exceder los 50 caracteres.',
        ];
    }
}
