<?
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskResult;

class TaskResultController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'task_name' => 'required|string',
            'platform_name' => 'required|string',
            'result' => 'required|string',
        ]);

        $taskResult = TaskResult::create([
            'user_id' => auth()->id(),
            'task_name' => $validatedData['task_name'],
            'platform_name' => $validatedData['platform_name'],
            'result' => $validatedData['result'],
        ]);

        return response()->json([
            'message' => 'Task result saved successfully',
            'data' => $taskResult
        ], 201);
    }
}
