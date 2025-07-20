<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Unique para el nombre
            $table->decimal('price', 8, 2);
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete(); // foreign key
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        //Drop the foreign constraint
        $table->dropForeign(['category_id']);
        //Drop the column
        $table->dropColummn('category_id');
        //Drop the table
        Schema::dropIfExists('products');
    }
};
