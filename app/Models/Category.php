<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Categorias
 * Maneja la instancia Category en la base de datos
 * @package Illuminate\Database\Eloquent\Model
 * @author Jean Carlo Garcia
 * @version 1.0
 * @since Laravel 12
*/
class Category extends Model
{
    use HasFactory;

    /**
     * Datos filiales
     * @param $name string
    */
    protected $fillable = ['name'];

    /**
     * Relación muchos a uno con Product
     * @return HasMany 
    */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}