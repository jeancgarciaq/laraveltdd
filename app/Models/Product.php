<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Modelo Productos
 * Maneja la instancia Product en la base de datos
 * @package Illuminate\Database\Eloquent\Model
 * @author Jean Carlo Garcia
 * @version 1.0
 * @since Laravel 12
*/
class Product extends Model
{
    use HasFactory;

    /**
     * Datos filiales
     * @param $name string
     * @param $price double
     * @param $descripcion text
     * @param $category_id foreign index
    */
    protected $fillable = ['name', 'price', 'description', 'category_id'];

    /**
     * Relación uno a muchos con Category
     * @return BelongsTo 
    */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}