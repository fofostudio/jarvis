<?php

namespace App\Http\Controllers;

use App\Models\WorkPlan;
use App\Models\Group;
use App\Services\WorkPlanGeneratorService;
use Illuminate\Http\Request;

class WorkPlanController extends Controller
{
    private $workPlanGenerator;

    public function __construct(WorkPlanGeneratorService $workPlanGenerator)
    {
        $this->workPlanGenerator = $workPlanGenerator;
    }

    public function index(Request $request)
    {
        $groups = Group::all();
        $selectedGroup = $request->input('group_id') ? Group::find($request->input('group_id')) : $groups->first();
        $week = $request->input('week', now()->weekOfYear);
        $year = $request->input('year', now()->year);

        $types = ['cartas', 'mensajes', 'icebreakers'];
        $workPlans = [];

        foreach ($types as $type) {
            $workPlan = WorkPlan::firstOrCreate(
                [
                    'group_id' => $selectedGroup->id,
                    'week_number' => $week,
                    'year' => $year,
                    'type' => $type
                ]
            );

            // Asegurarse de que haya una asignación para cada día y turno
            $shifts = ['mañana', 'tarde', 'madrugada'];
            for ($day = 1; $day <= 7; $day++) {
                foreach ($shifts as $shift) {
                    $workPlan->assignments()->firstOrCreate(
                        [
                            'day_of_week' => $day,
                            'shift' => $shift
                        ],
                        [
                            'girl_id' => $selectedGroup->girls->first()->id // Asigna la primera chica por defecto
                        ]
                    );
                }
            }

            $workPlan->load('assignments.girl');
            $workPlans[$type] = $workPlan;
        }

        return view('work_plans.index', compact('groups', 'selectedGroup', 'week', 'year', 'workPlans'));
    }
    public function generate(Request $request, WorkPlanGeneratorService $generator)
    {
        $group = Group::findOrFail($request->input('group_id'));
        $week = $request->input('week', now()->weekOfYear);
        $year = $request->input('year', now()->year);

        $generator->generateForGroup($group, $week, $year);

        return redirect()->route('work_plans.index', [
            'group_id' => $group->id,
            'week' => $week,
            'year' => $year
        ])->with('success', 'Planes de trabajo generados/regenerados exitosamente.');
    }

    public function update(Request $request, WorkPlan $workPlan)
    {
        $validatedData = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.girl_id' => 'required|exists:girls,id',
            'assignments.*.day_of_week' => 'required|integer|min:1|max:7',
            'assignments.*.shift' => 'required|in:mañana,tarde,madrugada',
        ]);

        foreach ($validatedData['assignments'] as $assignmentData) {
            $workPlan->assignments()->updateOrCreate(
                [
                    'day_of_week' => $assignmentData['day_of_week'],
                    'shift' => $assignmentData['shift'],
                ],
                [
                    'girl_id' => $assignmentData['girl_id'],
                ]
            );
        }

        return redirect()->route('work_plans.index', [
            'group_id' => $workPlan->group_id,
            'week' => $workPlan->week_number,
            'year' => $workPlan->year,
            'type' => $workPlan->type
        ])->with('success', 'Plan de trabajo actualizado exitosamente.');
    }
}
