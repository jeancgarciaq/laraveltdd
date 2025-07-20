<?php
// app/Policies/ProductPolicy.php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Política de autorización para el modelo Product
 *
 * Define las reglas de autorización para todas las operaciones
 * relacionadas con productos.
 *
 * @package App\Policies
 * @author Jean Carlo Garcia <jeancgarciaq@example.com>
 * @version 1.0.0
 * @since Laravel 12
 */
class ProductPolicy
{
    /**
     * Determina si el usuario puede ver cualquier modelo
     *
     * Permite a todos los usuarios autenticados ver la lista de productos.
     *
     * @param User $user Usuario que intenta la acción
     * @return bool True si el usuario puede ver productos
     */
    public function viewAny(User $user): bool
    {
        return true; // Cualquier usuario autenticado puede ver la lista
    }

    /**
     * Determina si el usuario puede ver el modelo específico
     *
     * Permite a todos los usuarios autenticados ver productos específicos.
     *
     * @param User $user Usuario que intenta la acción
     * @param Product $product Producto que se quiere ver
     * @return bool True si el usuario puede ver el producto
     */
    public function view(User $user, Product $product): bool
    {
        return true; // Cualquier usuario autenticado puede ver productos
    }

    /**
     * Determina si el usuario puede crear modelos
     *
     * @param User $user Usuario que intenta crear un producto
     * @return bool True si puede crear productos
     */
    public function create(User $user): bool
    {
        return true; // Cualquier usuario autenticado puede crear productos
    }

    /**
     * Determina si el usuario puede actualizar el modelo
     *
     * @param User $user Usuario que intenta la actualización
     * @param Product $product Producto que se quiere actualizar
     * @return bool True si puede actualizar el producto
     */
    public function update(User $user, Product $product): bool
    {
        // Aquí puedes agregar lógica específica si necesitas restricciones
        return true; // Por ahora, cualquier usuario puede actualizar
    }

    /**
     * Determina si el usuario puede eliminar el modelo
     *
     * @param User $user Usuario que intenta la eliminación
     * @param Product $product Producto que se quiere eliminar
     * @return bool True si puede eliminar el producto
     */
    public function delete(User $user, Product $product): bool
    {
        // Aquí puedes agregar lógica específica si necesitas restricciones
        return true; // Por ahora, cualquier usuario puede eliminar
    }

    /**
     * Determina si el usuario puede restaurar el modelo
     *
     * @param User $user Usuario que intenta la restauración
     * @param Product $product Producto eliminado que se quiere restaurar
     * @return bool True si puede restaurar el producto
     */
    public function restore(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede eliminar permanentemente el modelo
     *
     * @param User $user Usuario que intenta la eliminación permanente
     * @param Product $product Producto que se quiere eliminar permanentemente
     * @return bool False - eliminación permanente no permitida
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false; // No permitir eliminación permanente
    }
}