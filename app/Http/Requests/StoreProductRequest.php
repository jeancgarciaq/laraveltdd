<?php
// app/Http/Requests/StoreProductRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para almacenar nuevos productos
 *
 * Valida los datos de entrada cuando se crea un nuevo producto.
 *
 * @package App\Http\Requests
 * @author Jean Carlo Garcia <jeancgarciaq@example.com>
 * @version 1.0.0
 * @since Laravel 12
 */
class StoreProductRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en las políticas
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name', // ✅ Sin intentar acceder a route('product')
            'price' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.unique' => 'Ya existe un producto con este nombre.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio debe ser mayor a 0.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }

    /**
     * Obtiene nombres de atributos personalizados para los errores de validación
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'price' => 'precio',
            'description' => 'descripción',
            'category_id' => 'categoría',
        ];
    }
}