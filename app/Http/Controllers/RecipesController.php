<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\Ingredient;

class RecipesController extends Controller
{
    public function getWebhookUrl()
    {
        return response()->json([
            'webhook_url' => config('n8n_meal_researcher_webhook_url_test'),
        ]);
    }
    /**
     * Import a recipe from a JSON payload.
     */
    public function import(Request $request)
    {
        $data = $request->json()->all();

        foreach ($data as $entry) {
            if (isset($entry['output'])) {
                $output = $entry['output'];

                // Save the recipe
                $recipe = Recipe::create([
                    'name' => $output['title'],
                    'description' => $output['description'] ?? null,
                    'cook-time' => ($output['cook-time'] ?? ''),
                    'prep-time' => ($output['prep-time'] ?? ''),
                    'servings' => $output['nutrition_facts']['servings'] ?? null,
                    'source' => $output['link'] ?? null,
                    'firestoreCollectionId' => $output['firestoreCollectionId'] ?? null
                ]);

                // Save the ingredients
                if (isset($output['ingredients'])) {
                    foreach ($output['ingredients'] as $ingredientData) {
                        $ingredient = Ingredient::firstOrCreate(
                            ['name' => $ingredientData['name']],
                            ['default_unit' => null, 'category' => null]
                        );

                        $recipe->ingredients()->attach($ingredient->id, [
                            'amount' => $this->extractAmount($ingredientData['quantity']),
                            'unit' => $this->extractUnit($ingredientData['quantity']),
                        ]);
                    }
                }

                // Save the steps
                if (isset($output['steps'])) {
                    foreach (['preparation' => 'prep', 'execution' => 'cook'] as $stepType => $type) {
                        if (isset($output['steps'][$stepType])) {
                            foreach ($output['steps'][$stepType] as $index => $instruction) {
                                RecipeStep::create([
                                    'recipe_id' => $recipe->id,
                                    'step_number' => $index + 1,
                                    'instruction' => $instruction,
                                    'type' => $type,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['message' => 'Recipes imported successfully'], 201);
    }

    /**
     * Extract the numeric quantity from a string like "3 cups (381 grams)".
     */
    private function extractAmount($quantity)
    {
        preg_match('/^[\d.]+/', $quantity, $matches);
        return $matches[0] ?? null;
    }

    /**
     * Extract the unit from a string like "3 cups (381 grams)".
     */
    private function extractUnit($quantity)
    {
        preg_match('/[a-zA-Z]+/', $quantity, $matches);
        return $matches[0] ?? null;
    }
}
