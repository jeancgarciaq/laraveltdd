<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true), // ✅ Asegurar que es único
            'price' => fake()->randomFloat(2, 1.00, 999.99), // ✅ Precio mínimo 1.00
            'description' => fake()->sentence(),
            'category_id' => Category::factory(), // ✅ Esto creará una categoría automáticamente
        ];
    }
}
