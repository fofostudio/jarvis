<?php

namespace App\Http\Controllers;

use App\Models\BreakLog;
use App\Models\Group;
use App\Models\User;
use App\Models\Point;
use App\Models\Girl;
use App\Models\GroupOperator;
use App\Models\Platform;
use App\Models\SessionLog;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        if ($role == 'super_admin' || $role == 'admin') {
            return $this->superAdminDashboard();
        } elseif ($role == 'operator') {
            return $this->operatorDashboard($user);
        }

        // Fallback for unauthorized access
        return redirect()->route('home')->with('error', 'Unauthorized access');
    }

    public function superAdminDashboard()
    {
        $activeOperators = User::where('role', 'operator')
            ->whereHas('sessionLogs', function ($query) {
                $query->whereDate('date', Carbon::today());
            })->count();

        $activeOperatorsDetails = User::where('role', 'operator')
            ->whereHas('sessionLogs', function ($query) {
                $query->whereDate('date', Carbon::today());
            })
            ->with(['groups', 'sessionLogs' => function ($query) {
                $query->whereDate('date', Carbon::today())->latest();
            }, 'breakLogs' => function ($query) {
                $query->whereDate('start_time', Carbon::today());
            }])
            ->get()
            ->map(function ($operator) {
                $latestSessionLog = $operator->sessionLogs->first();
                $currentBreak = $operator->breakLogs->where('actual_end_time', null)->first();
                $breakTakenToday = $operator->breakLogs->isNotEmpty();

                $status = 'Laborando';
                if (!$latestSessionLog || $latestSessionLog->end_time) {
                    $status = 'Inactivo';
                } elseif ($currentBreak) {
                    $status = $currentBreak->overtime > 0 ? 'Excede Break' : 'Activo Break';
                }

                return [
                    'id_operador' => $operator->id,
                    'name' => $operator->name,
                    'current_group' => $this->getCurrentGroup($operator),
                    'session_start' => $latestSessionLog ? $latestSessionLog->first_login : null,
                    'session_end' => $latestSessionLog ? $latestSessionLog->last_logout : null,
                    'is_on_break' => $currentBreak !== null,
                    'break_start' => $currentBreak ? $currentBreak->start_time : null,
                    'break_overtime' => $currentBreak && $currentBreak->overtime > 0 ? $currentBreak->overtime : 0,
                    'status' => $status,
                    'break_taken' => $breakTakenToday,
                ];
            });

        $totalGroups = Group::count();
        $totalGirls = Girl::count();
        $totalPlatforms = Platform::count();

        $shifts = ['morning', 'afternoon', 'night'];
        $selectedShift = request('shift', 'morning');
        $latestPointsDate = Point::max('date');

        // Data for daily group chart
        $dailyGroupData = Point::select('group_id', DB::raw('SUM(points) as total_points'), DB::raw('SUM(goal) as total_goal'))
            ->whereDate('date', $latestPointsDate)
            ->where('shift', $selectedShift)
            ->groupBy('group_id')
            ->with('group')
            ->get();

        // Data for total points by day chart
        $dailyTotalData = Point::select('date', DB::raw('SUM(points) as total_points'), DB::raw('SUM(goal) as total_goal'))
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate indicators
        $todayPoints = Point::whereDate('date', Carbon::today())->where('shift', $selectedShift)->sum('points');
        $yesterdayPoints = Point::whereDate('date', Carbon::yesterday())->where('shift', $selectedShift)->sum('points');

        $thisMonthPoints = Point::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->where('shift', $selectedShift)
            ->sum('points');
        $lastMonthPoints = Point::whereMonth('date', Carbon::now()->subMonth()->month)
            ->whereYear('date', Carbon::now()->subMonth()->year)
            ->where('shift', $selectedShift)
            ->sum('points');

        $thisWeekPoints = Point::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('shift', $selectedShift)
            ->sum('points');
        $lastWeekPoints = Point::whereBetween('date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->where('shift', $selectedShift)
            ->sum('points');

        $totalPoints = Point::where('shift', $selectedShift)->sum('points');
        $totalGoal = Point::where('shift', $selectedShift)->sum('goal');

        $girlsPerPlatform = Platform::withCount('girls')->get();

        $currentMonthData = Point::selectRaw('DATE(date) as date, SUM(points) as total_points, SUM(goal) as total_goal')
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $previousMonthData = Point::selectRaw('DATE(date) as date, SUM(points) as total_points, SUM(goal) as total_goal')
            ->whereMonth('date', Carbon::now()->subMonth()->month)
            ->whereYear('date', Carbon::now()->subMonth()->year)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.superadmin', compact(
            'activeOperators',
            'activeOperatorsDetails',
            'totalGroups',
            'totalGirls',
            'totalPlatforms',
            'shifts',
            'selectedShift',
            'dailyGroupData',
            'dailyTotalData',
            'latestPointsDate',
            'todayPoints',
            'yesterdayPoints',
            'thisMonthPoints',
            'lastMonthPoints',
            'thisWeekPoints',
            'lastWeekPoints',
            'totalPoints',
            'totalGoal',
            'girlsPerPlatform',
            'currentMonthData',
            'previousMonthData'
        ));
    }


    private function getCurrentGroup($operator)
    {
        $currentShift = $this->getCurrentShift();
        return $operator->groups()
            ->first();
    }

    private function getCurrentShift()
    {
        $hour = now()->hour;
        if ($hour >= 6 && $hour < 14) {
            return 'morning';
        } elseif ($hour >= 14 && $hour < 22) {
            return 'afternoon';
        } else {
            return 'night';
        }
    }
    public function getMonthlyTotals(Request $request)
    {
        try {
            $period = $request->query('period', 'current');

            if ($period === 'current') {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
            } else {
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
            }

            $monthlyData = Point::selectRaw('DATE(date) as date, SUM(points) as total_points, SUM(goal) as total_goal')
                ->whereBetween('date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $formattedData = $monthlyData->map(function ($item) {
                return [
                    'month' => $item->date,
                    'total_points' => $item->total_points,
                    'total_goal' => $item->total_goal
                ];
            });

            return response()->json($formattedData);
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyTotals: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching monthly totals'], 500);
        }
    }
    public function toggleBreak($userId)
    {
        $operator = User::findOrFail($userId);
        $today = now()->toDateString();

        // Check if the operator has already taken a break today
        $todayBreak = $operator->breakLogs()
            ->whereDate('start_time', $today)
            ->first();

        if ($operator->is_on_break) {
            // If the operator is on break, end the break
            $breakLog = $operator->breakLogs()->whereNull('actual_end_time')->latest()->first();
            if ($breakLog) {
                $breakLog->actual_end_time = now();
                $breakLog->overtime = max(0, $breakLog->actual_end_time->diffInSeconds($breakLog->expected_end_time));
                $breakLog->save();

                $operator->is_on_break = false;
                $operator->save();

                return response()->json([
                    'success' => true,
                    'is_on_break' => false,
                    'message' => 'Break finalizado correctamente.',
                ]);
            }
        } elseif (!$todayBreak) {
            // If the operator hasn't taken a break today, start a new break
            $breakLog = new BreakLog([
                'user_id' => $operator->id,
                'start_time' => now(),
                'expected_end_time' => now()->addMinutes(30),
            ]);
            $breakLog->save();

            $operator->is_on_break = true;
            $operator->save();

            return response()->json([
                'success' => true,
                'is_on_break' => true,
                'message' => 'Break iniciado correctamente.',
            ]);
        } else {
            // If the operator has already taken a break today
            return response()->json([
                'success' => false,
                'message' => 'Ya has tomado tu break diario.',
            ], 400);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo procesar la solicitud de break.',
        ], 400);
    }


    private function operatorDashboard($user)
    {
        $currentShift = $this->getCurrentShift();
        $groupOperator = GroupOperator::where('user_id', $user->id)
            ->first();

        $assignedGroup = $groupOperator ? $groupOperator->group : null;
        $assignedGirls = $assignedGroup ? $assignedGroup->girls : collect();

        $operatorPoints = Point::where('user_id', $user->id)
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->orderBy('date')
            ->get();

        $totalPoints = $operatorPoints->sum('points');
        $totalGoal = $operatorPoints->sum('goal');

        $chartData = $operatorPoints->groupBy('date')->map(function ($items) {
            return [
                'date' => $items->first()->date,
                'points' => $items->sum('points'),
                'goal' => $items->sum('goal'),
            ];
        })->values();

        $lastLogins = SessionLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $isOnBreak = $user->is_on_break ?? false;

        // Obtener la jornada asignada
        $assignedShift = $currentShift ? ucfirst($currentShift) : 'No asignado';

        return view('dashboard.operator', compact(
            'assignedGroup',
            'assignedGirls',
            'totalPoints',
            'totalGoal',
            'chartData',
            'currentShift',
            'lastLogins',
            'groupOperator',
            'isOnBreak',
            'assignedShift'
        ));
    }
}
