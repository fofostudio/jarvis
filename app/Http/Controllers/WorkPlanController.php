<?php

namespace App\Http\Controllers;

use App\Models\WorkPlan;
use App\Models\Group;
use App\Models\GroupOperator;
use App\Models\User;
use App\Models\WorkPlanAssignment;
use App\Models\WorkPlanDetail;
use App\Services\WorkPlanGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function generate(Request $request)
    {
        $group = Group::findOrFail($request->input('group_id'));
        $week = $request->input('week', now()->weekOfYear);
        $year = $request->input('year', now()->year);

        $this->workPlanGenerator->generateForGroup($group, $week, $year);

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
    public function indexDetail(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $shift = $request->input('shift');

        // Obtener todos los turnos únicos
        $shifts = GroupOperator::distinct('shift')->pluck('shift');

        // Obtener todos los grupos
        $groups = Group::whereHas('category', function ($query) {
            $query->where('name', '!=', 'Administracion');
        })->orderBy('name', 'asc')->get();

        // Obtener indicadores de planes de trabajo por grupo para la fecha y turno seleccionados
        $groupIndicators = [];
        foreach ($groups as $group) {
            $query = WorkPlanDetail::whereDate('date', $date)
                ->whereHas('user.groupOperators', function ($q) use ($group, $shift) {
                    $q->where('group_id', $group->id);
                    if ($shift) {
                        $q->where('shift', $shift);
                    }
                });

            $groupIndicators[$group->id] = [
                'name' => $group->name,
                'mensajes' => (clone $query)->where('calendar_type', 'mensajes')->exists(),
                'icebreakers' => (clone $query)->where('calendar_type', 'icebreakers')->exists(),
                'cartas' => (clone $query)->where('calendar_type', 'cartas')->exists(),
            ];
        }

        // Obtener estadísticas generales
        $stats = [
            'total' => WorkPlanDetail::whereDate('date', $date)->count(),
            'mensajes' => WorkPlanDetail::whereDate('date', $date)->where('calendar_type', 'mensajes')->count(),
            'icebreakers' => WorkPlanDetail::whereDate('date', $date)->where('calendar_type', 'icebreakers')->count(),
            'cartas' => WorkPlanDetail::whereDate('date', $date)->where('calendar_type', 'cartas')->count(),
        ];

        // Obtener planes de trabajo (manteniendo los filtros existentes)
        $query = WorkPlanDetail::with(['user', 'user.groups', 'user.groupOperators']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('group_id')) {
            $query->whereHas('user.groupOperators', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('calendar_type')) {
            $query->where('calendar_type', $request->calendar_type);
        }

        if ($request->filled('shift')) {
            $query->whereHas('user.groupOperators', function ($q) use ($request) {
                $q->where('shift', $request->shift);
            });
        }

        $workPlans = $query->orderBy('created_at', 'desc')->get();

        $users = User::where('role', 'operator')->orderBy('name', 'asc')->get();

        return view('work_plans.indexDetail', compact('workPlans', 'users', 'groups', 'groupIndicators', 'stats', 'shifts'));
    }
    public function showDetail(WorkPlanDetail $workPlanDetail)
    {
        $workPlanAssignment = WorkPlanAssignment::where('work_plan_id', $workPlanDetail->id)
            ->with('girl')
            ->first();

        return view('work_plans.showDetail', compact('workPlanDetail', 'workPlanAssignment'));
    }
    public function editDetail(WorkPlanDetail $workPlanDetail)
    {
        $users = User::where('role', 'operator')->orderBy('name', 'asc')->get();
        $groups = Group::whereHas('category', function ($query) {
            $query->where('name', '!=', 'Administracion');
        })->orderBy('name', 'asc')->get();

        return view('work_plans.editDetail', compact('workPlanDetail', 'users', 'groups'));
    }
    public function updateDetail(Request $request, WorkPlanDetail $workPlanDetail)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'calendar_type' => 'required|in:mensajes,icebreakers,cartas',
            'cantidad' => 'required|integer|min:1',
        ]);

        $workPlanDetail->update($validatedData);

        return redirect()->route('work-detail.indexDetail')->with('success', 'Plan de trabajo actualizado exitosamente.');
    }
    public function destroyDetail(WorkPlanDetail $workPlanDetail)
    {
        $workPlanDetail->delete();

        return redirect()->route('work-detail.indexDetail')->with('success', 'Plan de trabajo eliminado exitosamente.');
    }

    public function myWorkPlan()
    {
        $user = auth()->user();
        $groupOperator = $user->groupOperators()->first();

        if (!$groupOperator) {
            \Log::warning('Usuario sin asignación de grupo y turno', ['user_id' => $user->id]);
            return view('work_plans.my_work_plan')->with('error', 'No tienes un grupo o turno asignado.');
        }

        $group = $groupOperator->group;
        $shift = $groupOperator->shift;

        $shiftMapping = [
            'morning' => 'mañana',
            'afternoon' => 'tarde',
            'night' => 'madrugada',
        ];

        $shiftInSpanish = $shiftMapping[$shift] ?? $shift;

        $currentDate = now();
        $weekNumber = $currentDate->weekOfYear;
        $year = $currentDate->year;
        $dayOfWeek = $currentDate->dayOfWeek;

        $workPlans = WorkPlan::where('group_id', $group->id)
            ->forWeek($weekNumber, $year)
            ->get();

        $assignments = collect(['mensajes' => null, 'icebreakers' => null, 'cartas' => null]);
        $completedPlans = collect(['mensajes' => false, 'icebreakers' => false, 'cartas' => false]);

        foreach ($workPlans as $workPlan) {
            $assignment = WorkPlanAssignment::where('work_plan_id', $workPlan->id)
                ->where('day_of_week', $dayOfWeek)
                ->where(function ($query) use ($shift, $shiftInSpanish) {
                    $query->where('shift', $shift)
                        ->orWhere('shift', $shiftInSpanish);
                })
                ->with('girl')
                ->first();

            if ($assignment) {
                $assignments[$workPlan->type] = $assignment;

                $completedPlans[$workPlan->type] = WorkPlanDetail::where('user_id', $user->id)
                    ->where('date', $currentDate->toDateString())
                    ->where('calendar_type', $workPlan->type)
                    ->exists();
            }
        }

        // Obtener todos los planes de trabajo cargados por el usuario
        $loadedPlans = WorkPlanDetail::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('work_plans.my_work_plan', compact('assignments', 'shift', 'shiftInSpanish', 'currentDate', 'group', 'weekNumber', 'year', 'dayOfWeek', 'completedPlans', 'loadedPlans'));
    }
    public function updateMyWorkPlan(Request $request)
    {
        $request->validate([
            'calendar_type' => 'required|in:mensajes,icebreakers,cartas',
            'cant' => 'required|integer',
            'mensaje' => 'required|string',
            'screenshots' => 'required|array',
            'screenshots.*' => 'image|max:2048', // 2MB Max
        ]);

        $user = auth()->user();
        $currentDate = now();

        // Verificar si ya existe un plan para este tipo de calendario y día
        $existingPlan = WorkPlanDetail::where('user_id', $user->id)
            ->where('date', $currentDate->toDateString())
            ->where('calendar_type', $request->calendar_type)
            ->first();

        if ($existingPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has cargado un plan de trabajo para este tipo de calendario hoy.'
            ], 422);
        }

        // Guardar los screenshots
        $screenshotPaths = [];
        foreach ($request->file('screenshots') as $screenshot) {
            $path = $screenshot->store('work_plan_screenshots', 'public');
            $screenshotPaths[] = $path;
        }

        // Crear el nuevo WorkPlanDetail
        $workPlanDetail = new WorkPlanDetail([
            'user_id' => $user->id,
            'date' => $currentDate->toDateString(),
            'calendar_type' => $request->calendar_type,
            'cantidad' => $request->cant,
            'mensaje' => $request->mensaje,
            'screenshot_paths' => json_encode($screenshotPaths),
        ]);
        $workPlanDetail->save();

        return response()->json([
            'success' => true,
            'message' => 'Plan de trabajo actualizado exitosamente.'
        ]);
    }
}
