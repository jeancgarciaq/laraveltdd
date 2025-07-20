<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controlador para la gestión de productos
 *
 * Maneja todas las operaciones CRUD para productos con autorización automática.
 *
 * @package App\Http\Controllers
 * @author Jean Carlo Garcia <jeancgarciaq@example.com>
 * @version 1.0.0
 * @since Laravel 12
 */
class ProductController extends Controller
{
    /**
     * Constructor del controlador
     *
     * Configura la autorización automática de recursos usando políticas.
     * Esto asegura que cada acción sea autorizada automáticamente.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Muestra la lista de productos
     *
     * @return View
     */
    public function index(): View
    {
        $products = Product::with('category')->get();
        return view('products.index', compact('products'));
    }

    /**
     * Muestra el formulario para crear un nuevo producto
     *
     * @return View
     */
    public function create(): View
    {
        $categories = Category::all(); // Obtener categorías para el formulario
        return view('products.create', compact('categories'));
    }

    /**
     * Almacena un nuevo producto en la base de datos
     *
     * @param StoreProductRequest $request 
     * @return RedirectResponse
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());
        return redirect()
            ->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Muestra un producto específico
     *
     * @param Product $product
     * @return View
     */
    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }

    /**
     * Muestra el formulario para editar un producto
     *
     * @param Product $product
     * @return View
     */
    public function edit(Product $product): View
    {
        $categories = Category::all(); // Obtener categorías para el formulario
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Actualiza un producto existente en la base de datos
     *
     * @param UpdateProductRequest $request
     * @param Product $product 
     * @return RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        // ✅ Corregido: incluir category_id en la actualización
        $product->update($request->validated());
        
        return redirect()
            ->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Elimina un producto de la base de datos
     *
     * @param Product $product 
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        
        return redirect()
            ->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}