<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class TeamLiderController extends Controller
{
    public function index()
    {
        $currentDate = now()->toDateString();
        $user = Auth::user();
        $tasks = $this->getOrCreateTasks($user, $currentDate);

        return view('team-lider.index', [
            'currentDate' => $currentDate,
            'tasks' => $tasks,
            'isCurrentDate' => true,
            'statusColors' => $this->getStatusColors()
        ]);
    }

    private function getStatusColors()
    {
        return [
            'Listo' => 'success',
            'En curso' => 'warning',
            'Detenido' => 'danger',
            'En espera' => 'info',
        ];
    }

    public function getTasks(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $user = Auth::user();
        $tasks = $this->getOrCreateTasks($user, $date);
        $isCurrentDate = $date === now()->toDateString();

        return response()->json([
            'tasks' => $tasks,
            'isCurrentDate' => $isCurrentDate
        ]);
    }

    private function getOrCreateTasks($user, $date)
    {
        $tasks = $user->tasks()->whereDate('task_date', $date)->get();

        if ($tasks->isEmpty()) {
            $defaultTasks = Task::where('is_default', true)->get();
            foreach ($defaultTasks as $defaultTask) {
                $user->tasks()->create([
                    'name' => $defaultTask->name,
                    'status' => 'En espera',
                    'task_date' => $date,
                    'is_default' => false
                ]);
            }
            $tasks = $user->tasks()->whereDate('task_date', $date)->get();
        }

        return $tasks;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:En espera,En curso,Listo,Detenido',
            'task_date' => 'required|date',
        ]);

        $task = Auth::user()->tasks()->create($validated + ['is_default' => false]);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        if (Auth::id() !== $task->user_id) {
            throw new AuthorizationException('You do not have permission to update this task.');
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:En espera,En curso,Listo,Detenido',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        if (Auth::id() !== $task->user_id) {
            throw new AuthorizationException('You do not have permission to delete this task.');
        }

        $task->delete();

        return response()->json(null, 204);
    }
}
