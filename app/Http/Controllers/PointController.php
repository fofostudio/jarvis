<?php

namespace App\Http\Controllers;

use App\Models\Girl;
use App\Models\Point;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupOperator;
use App\Models\Platform;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : now();
        $isFiltering = $request->boolean('filter', false);

        if ($isFiltering) {
            // Filtering for a specific day
            $points = Point::with(['user', 'group'])
                ->whereDate('date', $date)
                ->get();
        } else {
            // Loading data for the entire month
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $points = Point::with(['user', 'group'])
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->get();

            $calendarData = [];
            foreach ($monthStart->range($monthEnd) as $day) {
                $dayPoints = $points->where('date', $day->format('Y-m-d'));
                $shifts = $dayPoints->pluck('shift')->unique()->values()->toArray();
                $status = count($shifts) === 3 ? 'complete' : (count($shifts) > 0 ? 'partial' : 'none');

                $calendarData[$day->format('Y-m-d')] = [
                    'status' => $status,
                    'shifts' => $shifts,
                ];
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'records' => view('points.records', ['points' => $points])->render(),
                'calendarData' => $isFiltering ? null : $calendarData,
                'currentMonth' => $isFiltering ? null : $date->format('F Y'),
            ]);
        }

        // For initial page load
        return view('points.index', compact('points', 'calendarData', 'date'));
    }
    public function create()
    {
        $operators = User::where('role', 'operator')->get();
        $groupOperators = GroupOperator::with(['group', 'user'])->get();
        $shiftOptions = ['morning', 'afternoon', 'night'];
        return view('points.create', compact('operators', 'groupOperators', 'shiftOptions'));
    }
    public function groups(Request $request)
    {
        $shift = $request->input('shift');
        $groups = Group::all();
        $groupsWithOperators = [];

        try {
            foreach ($groups as $group) {
                $operator = $group->operators()->wherePivot('shift', $shift)->first();
                if ($operator) {
                    $groupsWithOperators[] = $group;
                }
            }

            return response()->json(['groups' => $groupsWithOperators]);
        } catch (\Exception $e) {
            // Log the error and return a generic error message
            Log::error('Error in PointController@groups: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the groups.'], 500);
        }
    }
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pointsFile' => 'required|file',
            'shift' => 'required|in:morning,afternoon,night',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $file = $request->file('pointsFile');
        $shift = $request->input('shift');
        $date = $request->input('date');

        $content = file_get_contents($file->getRealPath());
        $lines = explode("\n", $content);

        $preview = [];
        $allOperators = User::where('role', 'operator')->orderBy('name')->get();
        $totalPoints = 0;
        $totalGoal = 0;

        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) !== 3) {
                continue; // Skip invalid lines
            }

            $groupName = trim($data[0]);
            $points = (int)trim($data[1]);
            $goal = (int)trim($data[2]);

            $group = Group::where('name', $groupName)->first();
            if (!$group) {
                continue; // Skip if group not found
            }

            $assignedOperator = $group->operators()->wherePivot('shift', $shift)->first();

            $preview[] = [
                'group' => $groupName,
                'group_id' => $group->id,
                'points' => $points,
                'goal' => $goal,
                'assigned_operator_id' => $assignedOperator ? $assignedOperator->id : null,
                'operators' => $allOperators->map(function ($operator) {
                    return ['id' => $operator->id, 'name' => $operator->name];
                }),
            ];

            $totalPoints += $points;
            $totalGoal += $goal;
        }

        return response()->json(['success' => true, 'preview' => $preview, 'totalPoints' => $totalPoints, 'totalGoal' => $totalGoal]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift' => 'required|in:morning,afternoon,night',
            'date' => 'required|date',
            'points' => 'required|array',
            'goals' => 'required|array',
            'operators' => 'required|array',
            'points.*' => 'required|numeric|min:0',
            'goals.*' => 'required|numeric|min:0',
            'operators.*' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        DB::beginTransaction();

        try {
            $date = $request->input('date');
            $shift = $request->input('shift');
            $points = $request->input('points');
            $goals = $request->input('goals');
            $operators = $request->input('operators');
            $totalPoints = 0;
            $totalGoal = 0;

            foreach ($points as $groupId => $pointValue) {
                Point::create([
                    'user_id' => $operators[$groupId],
                    'group_id' => $groupId,
                    'date' => $date,
                    'shift' => $shift,
                    'points' => $pointValue,
                    'goal' => $goals[$groupId],
                ]);
                $totalPoints += $pointValue;
                $totalGoal += $goals[$groupId];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Points saved successfully.',
                'redirect' => route('points.index'),
                'totalPoints' => $totalPoints,
                'totalGoal' => $totalGoal
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving points: ' . $e->getMessage()
            ], 500);
        }
    }
    public function myPoints()
    {
        $user = auth()->user();
        $points = $user->points; // Asumiendo que tienes una relación 'points' en tu modelo User
        return view('operator.my_points', compact('points'));
    }

    public function show(Point $point)
    {
        return view('points.show', compact('point'));
    }

    public function edit(Point $point)
    {
        $users = User::where('role', 'operator')->get();
        return view('points.edit', compact('point', 'users'));
    }

    public function update(Request $request, Point $point)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer',
        ]);

        $point->update($validated);

        if ($request->ajax()) {
            $points = Point::whereDate('date', $point->date)->get();
            $records = view('points.records', compact('points'))->render();
            return response()->json(['success' => true, 'records' => $records]);
        }

        return redirect()->route('points.index')->with('success', 'Points updated successfully.');
    }

    public function destroy(Point $point)
    {
        $point->delete();

        return redirect()->route('points.index')->with('success', 'Points deleted successfully.');
    }
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'operator') {
            $points = Point::where('user_id', $user->id)->get();
        } else {
            $points = Point::all();
        }

        $totalPoints = $points->sum('points');
        $averagePoints = $points->avg('points');

        return view('points.dashboard', compact('points', 'totalPoints', 'averagePoints'));
    }
}
