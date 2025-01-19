<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'variationOf',
        'cook-time', // e.g., "45 mins"
        'prep-time', // e.g., "45 mins"
        'servings',
        'source', // URL or reference for the recipe,
        'firestoreCollectionId' //location of original recipe, if applicable
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'time' => 'string',
        'servings' => 'integer',
    ];

    /**
     * Relationships
     */

    // Parent recipe for variations
    public function origin()
    {
        return $this->belongsTo(self::class, 'variationOf');
    }

    // Variations of the recipe
    public function variations()
    {
        return $this->hasMany(self::class, 'variationOf');
    }

    // Ingredients used in the recipe
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('amount', 'unit')
            ->withTimestamps();
    }

    // Steps for the recipe
    public function steps()
    {
        return $this->hasMany(RecipeStep::class)->orderBy('step_number');
    }

    // Tags associated with the recipe
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'recipe_tags');
    }

    // Meals that include this recipe
    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'meal_recipes');
    }
}
