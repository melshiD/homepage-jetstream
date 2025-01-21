<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
        $n8nWebhookUrl = 'https://n8n-v96l.onrender.com/webhook/67fd4442-1c79-4957-b8b7-8162f2f06091';

        // Send data to n8n
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
        try {
            $data = $request->json()->all();

            // Validate request payload
            if (empty($data) || !is_array($data)) {
                return response()->json(['error' => 'Invalid payload format.'], 400);
            }

            $output = $data[0]['output'];
            $firestoreCollectionId = $data[1]['firestoreCollectionId'];

            // Validate required fields
            if (empty($output['title'])) {
                return response()->json(['error' => 'Recipe title is required.'], 400);
            }

            if (!isset($output['ingredients']) || !is_array($output['ingredients'])) {
                return response()->json(['error' => 'Ingredients are required and should be an array.'], 400);
            }

            if (!isset($output['steps']) || !is_array($output['steps'])) {
                return response()->json(['error' => 'Steps are required and should be an array.'], 400);
            }

            $recipe = Recipe::create([
                'name' => $output['title'],
                'description' => $output['description'] ?? null,
                'type' => $output['type'],
                'cook-time' => ($output['cookTime'] ?? null),
                'prep-time' => ($output['prepTime'] ?? null),
                'servings' => $output['nutrition_facts']['servings'] ?? null,
                'source' => $output['link'] ?? null,
                'firestoreCollectionId' => $firestoreCollectionId ?? null,
            ]);

            //         // Save the ingredients
            //         if (isset($output['ingredients']) && is_array($output['ingredients'])) {
            //             foreach ($output['ingredients'] as $ingredientData) {
            //                 if (empty($ingredientData['name'])) {
            //                     return response()->json(['error' => 'Ingredient name is required.'], 400);
            //                 }

            //                 $ingredient = Ingredient::firstOrCreate(
            //                     ['name' => $ingredientData['name']],
            //                     ['default_unit' => null, 'category' => null]
            //                 );

            //                 $recipe->ingredients()->attach($ingredient->id, [
            //                     'amount' => $this->extractAmount($ingredientData['quantity'] ?? ''),
            //                     'unit' => $this->extractUnit($ingredientData['quantity'] ?? ''),
            //                 ]);
            //             }
            //         } else {
            //             return response()->json(['error' => 'Ingredients are required and should be an array.'], 400);
            //         }

            //         // Save the steps
            //         if (isset($output['steps']) && is_array($output['steps'])) {
            //             foreach (['preparation' => 'prep', 'execution' => 'cook'] as $stepType => $type) {
            //                 if (isset($output['steps'][$stepType]) && is_array($output['steps'][$stepType])) {
            //                     foreach ($output['steps'][$stepType] as $index => $instruction) {
            //                         if (empty($instruction)) {
            //                             return response()->json(['error' => "Instruction at step $index is empty."], 400);
            //                         }

            //                         RecipeStep::create([
            //                             'recipe_id' => $recipe->id,
            //                             'step_number' => $index + 1,
            //                             'instruction' => $instruction,
            //                             'type' => $type,
            //                         ]);
            //                     }
            //                 }
            //             }
            //         } else {
            //             return response()->json(['error' => 'Steps are required and should be an array.'], 400);
            //         }
            //     } catch (Exception $e) {
            //         return response()->json(['error' => 'Error processing recipe: ' . $e->getMessage()], 500);
            //     }
            // }

            return response()->json(['message' => 'Writing to SQL database handled successfully', "recipe" => $recipe], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
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

    // Grab test recipe json from json file: helpful for minimizing api calls for development
    public function testRecipeJson()
    {
        $data = [
            'output' => [
                'title' => 'The Best Chocolate Chip Cookie Recipe Ever',
                'slug' => 'the-best-chocolate-chip-cookie-recipe-ever',
                'description' => 'This is the best chocolate chip cookies recipe ever! No funny ingredients, no chilling time, just a simple, straightforward, amazingly delicious, doughy yet fully cooked, chocolate chip cookie that turns out perfectly every single time!',
                'type' => 'recipe',
                'link' => 'https://joyfoodsunshine.com/the-most-amazing-chocolate-chip-cookies/',
                'prepTime' => '15 minutes',
                'cookTime' => '10 minutes',
                'ingredients' => [
                    ['name' => 'Salted butter, softened', 'quantity' => '1 cup'],
                    ['name' => 'Granulated sugar', 'quantity' => '1 cup'],
                    ['name' => 'Light brown sugar, packed', 'quantity' => '1 cup'],
                    ['name' => 'Pure vanilla extract', 'quantity' => '2 teaspoons'],
                    ['name' => 'Large eggs', 'quantity' => '2'],
                    ['name' => 'All-purpose flour', 'quantity' => '3 cups'],
                    ['name' => 'Baking soda', 'quantity' => '1 teaspoon'],
                    ['name' => 'Baking powder', 'quantity' => '½ teaspoon'],
                    ['name' => 'Sea salt', 'quantity' => '1 teaspoon'],
                    ['name' => 'Chocolate chips', 'quantity' => '2 cups (12 oz)'],
                ],
                'steps' => [
                    'preparation' => [
                        'Preheat oven to 375 degrees F. Line three baking sheets with parchment paper and set aside.',
                        'In a medium bowl, mix flour, baking soda, baking powder, and salt. Set aside.',
                        'Cream together butter and sugars until combined.',
                        'Beat in eggs and vanilla until light (about 1 minute).',
                        'Mix in the dry ingredients until combined.',
                        'Add chocolate chips and mix well.',
                        'Roll 2-3 Tablespoons of dough into balls and place them evenly spaced on prepared cookie sheets.',
                    ],
                    'execution' => [
                        'Bake in preheated oven for approximately 8-10 minutes, taking them out when they are just barely starting to turn brown.',
                        'Let them sit on the baking pan for 5 minutes before transferring to a cooling rack.',
                    ],
                ],
                'additionalInfo' => [
                    'usefulTips' => [
                        'Do not over-bake the cookies; they will look doughy when you remove them from the oven, which is desired.',
                    ],
                ],
                'nutrition_facts' => [
                    'servings' => 36,
                    'calories_per_serving' => 183,
                    'macronutrients' => [
                        'protein' => ['value' => 2, 'unit' => 'g'],
                        'carbohydrates' => ['value' => 26, 'unit' => 'g'],
                        'fats' => ['value' => 8, 'unit' => 'g'],
                        'saturated_fat' => ['value' => 5, 'unit' => 'g'],
                    ],
                    'micronutrients' => [
                        'sodium' => ['value' => 153, 'unit' => 'mg'],
                        'cholesterol' => ['value' => 27, 'unit' => 'mg'],
                        'fiber' => ['value' => 1, 'unit' => 'g'],
                        'sugar' => ['value' => 18, 'unit' => 'g'],
                        'potassium' => ['value' => 31, 'unit' => 'mg'],
                        'vitamin_a' => ['value' => 197, 'unit' => 'IU'],
                        'vitamin_c' => ['value' => 1, 'unit' => 'mg'],
                        'calcium' => ['value' => 24, 'unit' => 'mg'],
                        'iron' => ['value' => 1, 'unit' => 'mg'],
                    ],
                ],
            ],
            'firestoreCollectionId' => 'the-best-chocolate-chip-cookie-recipe-ever-65thsius',
        ];

        $anotherWay = Storage::get('testRecipeResponse.json');
        $anotherWayJson = json_decode($anotherWay, true);

        return response()->json($data);
    }
}
