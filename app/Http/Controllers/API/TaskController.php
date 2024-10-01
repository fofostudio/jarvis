<?
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:En espera,En curso,Listo,Detenido',
            'task_date' => 'date',
        ]);

        $task = Auth::user()->tasks()->create($validated);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        // Verificar que el usuario autenticado es el propietario de la tarea
        $this->authorize('update', $task);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:En espera,En curso,Listo,Detenido',
        ]);

        $task->update($validated);

        return response()->json($task);
    }
}