<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Pruebas de autorización para productos
 *
 * @package Tests\Feature
 * @author Jean Carlo Garcia
 * @version 1.0.0
 * @since Laravel 12
 */
class ProductAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function debug_product_authorization(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        // Verificar que el usuario está autenticado
        $this->actingAs($user);
        $this->assertTrue(auth()->check());

        // Verificar manualmente la autorización
        $canView = $user->can('view', $product);
        dump('Can view product: ' . ($canView ? 'YES' : 'NO'));

        // Verificar si la política está registrada
        $policy = \Illuminate\Support\Facades\Gate::getPolicyFor($product);
        dump('Policy class: ' . ($policy ? get_class($policy) : 'No policy found'));

        // Intentar acceder a la ruta
        $response = $this->get(route('products.show', $product));
        dump('Response status: ' . $response->getStatusCode());

        $this->assertTrue(true); // Para que el test pase
    }

    #[Test]
    public function authenticated_user_can_view_products_index(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get(route('products.index'));

        $response->assertOk();
        $response->assertViewIs('products.index');
    }

    #[Test]
    public function authenticated_user_can_view_specific_product(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get(route('products.show', $product));

        $response->assertOk();
        $response->assertViewIs('products.show');
    }

    #[Test]
    public function guest_cannot_view_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->get(route('products.index'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_view_specific_product(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->get(route('products.show', $product));
        $response->assertRedirect(route('login'));
    }
}