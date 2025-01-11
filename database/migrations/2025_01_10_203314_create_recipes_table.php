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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();  // e.g., main, side
            $table->json('prep_steps')->nullable();
            $table->json('cook_steps')->nullable();
            $table->string('time')->nullable();  // e.g., 45 mins
            $table->integer('servings')->nullable();
            $table->string('difficulty')->nullable();  // e.g., easy, medium, hard
            $table->integer('rating')->nullable();
            $table->json('tags')->nullable();
            $table->string('origin')->nullable();  // country of origin
            $table->string('source')->nullable();  // source URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
