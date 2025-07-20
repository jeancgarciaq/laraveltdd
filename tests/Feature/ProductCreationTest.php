<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Pruebas de funcionalidad para la creación de productos
 *
 * @package Tests\Feature
 * @author Jean Carlo Garcia
 * @version 1.0.0
 * @since Laravel 12
 */
class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_authenticated_user_can_create_a_product_with_valid_data(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Laptop Pro X',
            'price' => 1299.99,
            'description' => 'Powerful and sleek laptop for professionals.',
            'category_id' => $category->id,
        ]);

        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('products', [
            'name' => 'Laptop Pro X',
            'price' => 1299.99,
            'category_id' => $category->id,
        ]);
        
        $this->assertCount(1, Product::all());
        
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Producto creado exitosamente.');
    }

    #[Test]
    public function a_product_requires_a_name(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => '',
            'price' => 10.00,
            'description' => 'Some description',
            'category_id' => $category->id,
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('products', 0);
    }

    #[Test]
    public function a_product_name_must_be_unique(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // ✅ Crear producto existente asociado a la categoría
        Product::factory()->create([
            'name' => 'Existing Product',
            'category_id' => $category->id
        ]);

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Existing Product',
            'price' => 20.00,
            'description' => 'Another product',
            'category_id' => $category->id,
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('products', 1);
    }

    #[Test]
    public function a_product_requires_a_valid_price(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // Precio no numérico
        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Test Product',
            'price' => 'abc',
            'description' => 'Description',
            'category_id' => $category->id,
        ]);
        
        $response->assertSessionHasErrors('price');
        $this->assertDatabaseCount('products', 0);

        // Precio menor a 0.01
        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Test Product 2',
            'price' => 0.00,
            'description' => 'Description 2',
            'category_id' => $category->id,
        ]);
        
        $response->assertSessionHasErrors('price');
        $this->assertDatabaseCount('products', 0);
    }

    #[Test]
    public function a_product_requires_an_existing_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Product without Category',
            'price' => 50.00,
            'description' => 'Description',
            'category_id' => 999,
        ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertDatabaseCount('products', 0);
    }

    #[Test]
    public function guests_cannot_create_products(): void
    {
        $category = Category::factory()->create();
        
        $response = $this->post(route('products.store'), [
            'name' => 'Unauthorized Product',
            'price' => 100.00,
            'category_id' => $category->id,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('products', 0);
    }
}