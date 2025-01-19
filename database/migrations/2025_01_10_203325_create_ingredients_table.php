<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('ingredients', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('type')->nullable();  // e.g., dry, fresh
        $table->string('category')->nullable();  // e.g., dairy, vegetable
        $table->string('default_unit')->nullable();  // e.g., grams, cups
        $table->float('price_per_unit')->nullable();
        $table->float('calories')->nullable();
        $table->float('fat')->nullable();
        $table->float('saturated_fat')->nullable();
        $table->float('cholesterol')->nullable();
        $table->float('sodium')->nullable();
        $table->float('potassium')->nullable();
        $table->float('carbohydrates')->nullable();
        $table->float('fiber')->nullable();
        $table->float('sugar')->nullable();
        $table->float('protein')->nullable();
        $table->float('vitamin_c')->nullable();
        $table->float('calcium')->nullable();
        $table->float('iron')->nullable();
        $table->float('vitamin_d')->nullable();
        $table->float('vitamin_b6')->nullable();
        $table->float('cobalamin')->nullable();
        $table->float('magnesium')->nullable();
        $table->json('allergens')->nullable();
        $table->json('notes')->nullable();  // Structured notes
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
