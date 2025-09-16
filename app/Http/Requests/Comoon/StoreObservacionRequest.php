<?php

namespace App\Http\Requests\Comoon;

use Illuminate\Foundation\Http\FormRequest;

class StoreObservacionRequest extends FormRequest
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
            'observaciones' => 'required|string|max:1000'
        ];
    }
}
