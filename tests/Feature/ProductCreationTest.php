<?php

namespace Tests\Feature;

use App\Models\Category; // Asumimos un modelo Category
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_authenticated_user_can_create_a_product_with_valid_data()
    {
        // 1. Arrange: Crear y autenticar un usuario, y una categoría.
        $user = User::factory()->create();
        $category = Category::factory()->create(); // Necesitamos una categoría existente para la validación 'exists'
        $this->actingAs($user);

        // 2. Act: Simular una petición POST para crear un producto con datos válidos.
        $response = $this->post('/products', [
            'name' => 'Laptop Pro X',
            'price' => 1299.99,
            'description' => 'Powerful and sleek laptop for professionals.',
            'category_id' => $category->id,
        ]);

        // 3. Assert:
        // Verificar que el producto fue almacenado en la base de datos.
        $this->assertDatabaseHas('products', [
            'name' => 'Laptop Pro X',
            'price' => 1299.99,
            'category_id' => $category->id,
        ]);
        $this->assertCount(1, Product::all()); // Asegurarse de que solo se creó 1 producto

        // Verificar la redirección y el mensaje de éxito.
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Producto creado exitosamente.');
    }

    #[Test]
    public function a_product_requires_a_name()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/products', [
            'name' => '', // Falla: nombre vacío
            'price' => 10.00,
            'description' => 'Some description',
            'category_id' => $category->id,
        ]);

        $response->assertSessionHasErrors('name'); // Esperamos error para 'name'
        $this->assertDatabaseCount('products', 0); // No se debe guardar nada
    }

    #[Test]
    public function a_product_name_must_be_unique()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);

        // Crear un producto existente con el mismo nombre.
        Product::factory()->create(['name' => 'Existing Product']);

        $response = $this->post('/products', [
            'name' => 'Existing Product', // Falla: nombre duplicado
            'price' => 20.00,
            'description' => 'Another product',
            'category_id' => $category->id,
        ]);

        $response->assertSessionHasErrors('name'); // Esperamos error para 'name'
        $this->assertDatabaseCount('products', 1); // Solo debe haber 1 producto (el existente)
    }

    #[Test]
    public function a_product_requires_a_valid_price()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);

        // Intento 1: Precio no numérico
        $response = $this->post('/products', [
            'name' => 'Test Product',
            'price' => 'abc', // Falla: no numérico
            'description' => 'Description',
            'category_id' => $category->id,
        ]);
        $response->assertSessionHasErrors('price');
        $this->assertDatabaseCount('products', 0);

        // Intento 2: Precio menor a 0.01
        $response = $this->post('/products', [
            'name' => 'Test Product 2',
            'price' => 0.00, // Falla: menor a 0.01
            'description' => 'Description 2',
            'category_id' => $category->id,
        ]);
        $response->assertSessionHasErrors('price');
        $this->assertDatabaseCount('products', 0);
    }

    #[Test]
    public function a_product_requires_an_existing_category()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/products', [
            'name' => 'Product without Category',
            'price' => 50.00,
            'description' => 'Description',
            'category_id' => 999, // Falla: ID de categoría no existente
        ]);

        $response->assertSessionHasErrors('category_id'); // Esperamos error para 'category_id'
        $this->assertDatabaseCount('products', 0);
    }

    #[Test]
    public function guests_cannot_create_products()
    {
        $category = Category::factory()->create();
        $response = $this->post('/products', [
            'name' => 'Unauthorized Product',
            'price' => 100.00,
            'category_id' => $category->id,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseCount('products', 0);
    }
}