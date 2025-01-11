namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'meal_type', 'scheduled_date'];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'meal_recipes');
    }
}
