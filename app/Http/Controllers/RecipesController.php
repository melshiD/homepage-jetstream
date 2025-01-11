<?php
namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecipesController extends Controller
{
    public function store(Request $request)
    {
        // Step 1: Extract recipe data
        $recipeData = $request->all();

        // Step 2: Check if the recipe already exists
        $recipe = Recipe::firstOrCreate(
            ['name' => $recipeData['title']],
            ['prep_steps' => json_encode($recipeData['preparation']), 'cook_steps' => json_encode($recipeData['execution'])]
        );

        // Step 3: Process ingredients
        foreach ($recipeData['ingredients'] as $ingredientData) {
            $ingredient = Ingredient::firstOrCreate(['name' => $ingredientData['name']]);

            // Step 4: Attach ingredient to the recipe with quantity
            $recipe->ingredients()->attach($ingredient->id, [
                'quantity' => $ingredientData['quantity'],
                'unit' => $this->extractUnit($ingredientData['quantity'])
            ]);
        }

        // Step 5: Save nutrition facts
        if (isset($recipeData['nutrition'])) {
            $recipe->nutrition()->create([
                'servings' => $recipeData['nutrition']['servings'],
                'calories_per_serving' => $recipeData['nutrition']['calories_per_serving'],
                'macronutrients' => json_encode($recipeData['nutrition']['macronutrients']),
                'micronutrients' => json_encode($recipeData['nutrition']['micronutrients']),
                'additional_notes' => json_encode($recipeData['nutrition']['additional_notes']),
            ]);
        }

        // Step 6: Return success response
        return response()->json(['message' => 'Recipe stored successfully', 'recipe' => $recipe]);
    }

    // Helper function to extract unit from quantity
    private function extractUnit($quantity)
    {
        preg_match('/[a-zA-Z]+/', $quantity, $matches);
        return $matches[0] ?? null;
    }
}
