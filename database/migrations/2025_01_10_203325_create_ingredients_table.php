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
            $table->string('type')->nullable();  // e.g., dry, fresh
            $table->string('quantity')->nullable();
            $table->float('calories')->nullable();
            $table->float('fat')->nullable();
            $table->float('protein')->nullable();
            $table->float('carbs')->nullable();
            $table->float('fiber')->nullable();
            $table->float('sugar')->nullable();
            $table->float('sodium')->nullable();
            $table->float('cholesterol')->nullable();
            $table->json('allergens')->nullable();
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
