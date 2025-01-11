<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'quantity', 'calories', 'fat', 'protein', 'carbs', 'fiber', 'sugar', 'sodium', 'cholesterol', 'allergens'];

    protected $casts = [
        'allergens' => 'array',
    ];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients')->withPivot('quantity', 'unit');
    }
}
