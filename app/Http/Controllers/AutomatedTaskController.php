<?php

namespace App\Http\Controllers;

use App\Models\Girl;
use App\Models\Platform;
use Illuminate\Http\Request;

class AutomatedTaskController extends Controller
{
    public function platformTasks(Platform $platform)
    {
        // Lógica para mostrar las tareas automatizadas de la plataforma
        return view('operator.automated_tasks', compact('platform'));
    }
    public function showGirlTasks(Request $request, Platform $platform, Girl $girl)
    {
        // Verificar si el operador tiene acceso a esta chica
        $userGroups = $request->user()->groups->pluck('id')->toArray();
        if (!in_array($girl->group_id, $userGroups) || $girl->platform_id !== $platform->id) {
            abort(403, 'No tienes permiso para ver las tareas de esta chica.');
        }

        // Aquí puedes cargar las tareas automatizadas específicas de la chica
        // Por ahora, solo pasaremos la información de la plataforma y la chica a la vista

        return view('automated_tasks.girl', compact('platform', 'girl'));
    }
    public function showPlatformTasks(Platform $platform)
    {
        // Verificar si el operador tiene acceso a esta plataforma
        $userGroups = auth()->user()->groups->pluck('id')->toArray();
        $hasAccess = $platform->girls()->whereIn('group_id', $userGroups)->exists();

        if (!$hasAccess) {
            abort(403, 'No tienes permiso para ver las tareas de esta plataforma.');
        }

        // Aquí puedes cargar las tareas automatizadas específicas de la plataforma
        // Por ejemplo:
        // $tasks = $platform->automatedTasks; // Asumiendo que tienes una relación definida

        return view('automated_tasks.platform', compact('platform'));
    }
}
