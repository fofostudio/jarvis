<?php

namespace App\Services;

use App\Models\WorkPlan;
use App\Models\Girl;
use App\Models\Group;
use Illuminate\Support\Collection;

class WorkPlanGeneratorService
{
    public function generateForGroup(Group $group, int $weekNumber, int $year)
    {
        $types = ['cartas', 'mensajes', 'icebreakers'];
        $previousDayAssignments = [];

        foreach (range(1, 7) as $day) {
            // Guardar las chicas asignadas por día y tipo de calendario
            $dailyAssignments = [];
            foreach ($types as $type) {
                $this->createOrUpdateWorkPlanForType($group, $weekNumber, $year, $type, $day, $dailyAssignments, $previousDayAssignments);
            }
            // Guardar las asignaciones del día para el control del día siguiente
            $previousDayAssignments[$day] = $dailyAssignments;
        }
    }

    private function createOrUpdateWorkPlanForType(Group $group, int $weekNumber, int $year, string $type, int $day, array &$dailyAssignments, array $previousDayAssignments)
    {
        $girls = $group->girls->shuffle();  // Mezclar las chicas para asegurar aleatoriedad
        $shifts = ['mañana', 'tarde', 'madrugada'];

        $workPlan = WorkPlan::updateOrCreate(
            [
                'group_id' => $group->id,
                'week_number' => $weekNumber,
                'year' => $year,
                'type' => $type
            ]
        );

        // Eliminar asignaciones existentes para este día y tipo
        $workPlan->assignments()->where('day_of_week', $day)->delete();

        $assignments = $this->generateAssignmentsForType($girls, $shifts, $dailyAssignments, $previousDayAssignments, $day, $type);

        // Mezclar las asignaciones para evitar patrones predecibles
        shuffle($assignments);

        // Insertar todas las asignaciones de una vez
        $workPlan->assignments()->createMany($assignments);

        // Guardar las asignaciones de chicas para este tipo de calendario en el día
        $dailyAssignments[$type] = collect($assignments)->pluck('girl_id')->toArray();
    }

    private function generateAssignmentsForType(Collection $girls, array $shifts, array $dailyAssignments, array $previousDayAssignments, int $day, string $type)
    {
        $assignments = [];

        foreach ($shifts as $shift) {
            // Obtener una chica que no haya sido asignada en otro calendario en este día ni en el mismo calendario en el día anterior
            $girl = $this->getAvailableGirl($girls, $dailyAssignments, $previousDayAssignments, $day, $shift, $type);

            $assignments[] = [
                'girl_id' => $girl->id,
                'day_of_week' => $day,
                'shift' => $shift
            ];

            // Remover la chica asignada para que no se repita en este día
            $girls = $girls->filter(fn($g) => $g->id !== $girl->id);
        }

        return $assignments;
    }

    private function getAvailableGirl(Collection $girls, array $dailyAssignments, array $previousDayAssignments, int $day, string $shift, string $type)
    {
        // Filtrar chicas que no hayan sido asignadas en este día a ningún otro tipo de calendario
        $filteredGirls = $girls->filter(function ($girl) use ($dailyAssignments, $previousDayAssignments, $day, $type) {
            // Verificar si la chica ya fue asignada en este mismo día a otro tipo de calendario
            foreach ($dailyAssignments as $assignedType => $girlsForType) {
                if (in_array($girl->id, $girlsForType)) {
                    return false; // La chica ya está asignada en otro calendario en este día
                }
            }

            // Verificar si la chica fue asignada en el mismo calendario el día anterior
            $previousDay = $day - 1;
            if (isset($previousDayAssignments[$previousDay]) && isset($previousDayAssignments[$previousDay][$type])) {
                if (in_array($girl->id, $previousDayAssignments[$previousDay][$type])) {
                    return false; // La chica ya está asignada en el mismo tipo de calendario el día anterior
                }
            }

            return true; // La chica está disponible para asignarse
        });

        // Si no hay chicas disponibles sin repetir, tomar una al azar
        if ($filteredGirls->isEmpty()) {
            return $girls->shuffle()->first();
        }

        return $filteredGirls->shuffle()->first();
    }
}
