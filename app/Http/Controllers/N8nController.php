<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\Ingredient;

class N8nController extends Controller
{
    public function getWebhookUrl()
    {
        return response()->json([
            'webhook_url' => config('app.n8n_meal_researcher_webhook_url_prod'),
        ]);
    }
    /**
     * Trigger the n8n workflow.
     */
    public function triggerWorkflow(Request $request)
    {
        // Replace with your actual n8n webhook URL
        $n8nWebhookUrl = "https://n8n-v96l.onrender.com/webhook/67fd4442-1c79-4957-b8b7-8162f2f06091";

        // Send data to n8n
        dd($request->all());
        $response = Http::post($n8nWebhookUrl, $request->all());

        if ($response->successful()) {
            dd($response->json());
            return response()->json(['message' => 'Workflow triggered successfully', 'data' => $response->json()], 200);
        }
        return response()->json(['error' => 'Failed to trigger workflow'], 500);
    }

    /**
     * Handle the webhook response from n8n.
     */
    public function handleResearchResponse(Request $request)
    {
        $data = $request->json()->all();
dd($data);
        foreach ($data as $entry) {
            if (isset($entry['output'])) {
                $output = $entry['output'];

                // Save the recipe
                $recipe = Recipe::create([
                    'name' => $output['title'],
                    'description' => $output['description'] ?? null,
                    'time' => ($output['prepTime'] ?? '') . ' + ' . ($output['cookTime'] ?? ''),
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

        return response()->json(['message' => 'Webhook handled successfully'], 201);
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
