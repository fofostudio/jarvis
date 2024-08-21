<?
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskResult extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'task_name', 'platform_name', 'result'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
