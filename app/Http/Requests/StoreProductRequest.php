<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Aquí puedes definir la lógica de autorización.
        // Por ejemplo, solo los administradores pueden crear productos.
        // return $this->user()->isAdmin();
        return true; // Por ahora, permitimos a cualquiera que llegue a esta validación
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:products'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'], // Asegura que la categoría exista
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'price.min' => 'El precio debe ser al menos :min.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
        ];
    }
}