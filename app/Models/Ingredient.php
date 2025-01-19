<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'category',
        'default_unit',
        'price_per_unit',
        'calories',
        'fat',
        'saturated_fat',
        'cholesterol',
        'sodium',
        'potassium',
        'carbohydrates',
        'fiber',
        'sugar',
        'protein',
        'vitamin_c',
        'calcium',
        'iron',
        'vitamin_d',
        'vitamin_b6',
        'cobalamin',
        'magnesium',
        'allergens',
        'notes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price_per_unit' => 'float',
        'calories' => 'float',
        'fat' => 'float',
        'saturated_fat' => 'float',
        'cholesterol' => 'float',
        'sodium' => 'float',
        'potassium' => 'float',
        'carbohydrates' => 'float',
        'fiber' => 'float',
        'sugar' => 'float',
        'protein' => 'float',
        'vitamin_c' => 'float',
        'calcium' => 'float',
        'iron' => 'float',
        'vitamin_d' => 'float',
        'vitamin_b6' => 'float',
        'cobalamin' => 'float',
        'magnesium' => 'float',
        'allergens' => 'array',
        'notes' => 'array',
    ];

    /**
     * Relationships
     */

    // Recipes that use this ingredient
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients')
            ->withPivot('amount', 'unit');
    }
}
