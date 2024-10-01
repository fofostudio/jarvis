<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $defaultTasks = [
            ['name' => 'revisión y envió de meet a la operadora KIM', 'status' => 'Detenido'],
            ['name' => 'revisar quienes no llegaron a turno y prestar grupos', 'status' => 'Listo'],
            ['name' => 'entrega de celulares', 'status' => 'Listo'],
            ['name' => 'revisar los gmails', 'status' => 'En curso'],
            ['name' => 'Tomar Asistencia', 'status' => 'En curso'],
            ['name' => 'Ingreso de Jarvis', 'status' => 'En curso'],
            ['name' => 'verificar el ingreso en plataformas Chat', 'status' => 'En curso'],
            ['name' => 'Que lean los reportes', 'status' => 'En curso'],
            ['name' => 'Grupo Soacha MEET', 'status' => 'En curso'],
            ['name' => 'Enviar reporte de asistencia', 'status' => 'En curso'],
            ['name' => 'Verificar quienes tendrán apoyo', 'status' => 'En curso'],
            ['name' => 'verificación de icebreakers', 'status' => 'En curso'],
            ['name' => 'verificación de historias', 'status' => 'En curso'],
            ['name' => 'seguimientos plan de trabajo', 'status' => 'En curso'],
            ['name' => 'revisar corte de las 6 o 10 para compañamiento', 'status' => 'En curso'],
            ['name' => 'responder cartas de amolatina', 'status' => 'En curso'],
            ['name' => 'revisión de reportes', 'status' => 'En curso'],
            ['name' => 'revisar que todos cierren jarvis', 'status' => 'En curso'],
        ];

        foreach ($defaultTasks as $task) {
            Task::create([
                'name' => $task['name'],
                'status' => $task['status'],
                'is_default' => true,
                'task_date' => now()->toDateString(),
                'user_id' => 1  // Asignar a un usuario administrador o por defecto
            ]);
        }
    }
}