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
            $table->text('description')->nullable();
            $table->foreignId('variationOf')->nullable()->constrained('recipes')->onDelete('cascade');
            $table->string('name');
            $table->string('type')->nullable();  // e.g., main, side
            $table->string('prep-time')->nullable();  // e.g., 45 mins
            $table->string('cook-time')->nullable();  // e.g., 45 mins
            $table->integer('servings')->nullable();
            $table->string('difficulty')->nullable();  // e.g., easy, medium, hard
            $table->string('origin')->nullable();  // country of origin
            $table->string('source')->nullable();  // source URL
            $table->string('firestoreCollectionId')->nullable();
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
