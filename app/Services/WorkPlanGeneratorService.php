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
        foreach ($types as $type) {
            $this->createOrUpdateWorkPlan($group, $weekNumber, $year, $type);
        }
    }

    private function createOrUpdateWorkPlan(Group $group, int $weekNumber, int $year, string $type)
    {
        $workPlan = WorkPlan::updateOrCreate(
            [
                'group_id' => $group->id,
                'week_number' => $weekNumber,
                'year' => $year,
                'type' => $type
            ]
        );

        // Eliminar asignaciones existentes
        $workPlan->assignments()->delete();

        $girls = $group->girls->shuffle();
        $shifts = ['mañana', 'tarde', 'madrugada'];
        $daysOfWeek = range(1, 7);

        $assignments = [];

        foreach ($daysOfWeek as $day) {
            $dailyGirls = $girls->shuffle();
            foreach ($shifts as $shiftIndex => $shift) {
                $girlIndex = ($shiftIndex + $day) % $girls->count();
                $girl = $dailyGirls[$girlIndex];
                $assignments[] = [
                    'girl_id' => $girl->id,
                    'day_of_week' => $day,
                    'shift' => $shift
                ];
            }
        }

        // Mezclar las asignaciones para evitar patrones predecibles
        shuffle($assignments);

        // Insertar todas las asignaciones de una vez
        $workPlan->assignments()->createMany($assignments);
    }
}
