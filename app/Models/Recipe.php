<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'prep_steps', 'cook_steps', 'time', 'servings', 'difficulty', 'rating', 'tags', 'origin', 'source'];

    protected $casts = [
        'prep_steps' => 'array',
        'cook_steps' => 'array',
        'tags' => 'array',
    ];

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'meal_recipes');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }
}