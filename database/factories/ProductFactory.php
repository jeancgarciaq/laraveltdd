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
            'name' => $this->faker->unique()->sentence(2),
            'price' => $this->faker->randomFloat(2, 0.01, 1000),
            'description' => $this->faker->paragraph(),
            'category_id' => Category::factory(), // Asocia a una categoría
        ];
    }
}
