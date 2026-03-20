<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CargarDocumentoCuentaCobroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // El usuario debe ser el contratista de la cuenta
        return $this->user()->id === $this->cuenta_cobro->contractor_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'numero_documento' => ['required', 'integer', 'between:1,16'],
            'archivo' => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'numero_documento.required' => 'El número de documento es requerido.',
            'numero_documento.integer' => 'El número de documento debe ser un número entero.',
            'numero_documento.between' => 'El número de documento debe estar entre 1 y 16.',
            'archivo.required' => 'El archivo es requerido.',
            'archivo.file' => 'El archivo debe ser un archivo válido.',
            'archivo.mimes' => 'El archivo debe ser un PDF.',
            'archivo.max' => 'El archivo no debe exceder 10MB.',
        ];
    }
}
