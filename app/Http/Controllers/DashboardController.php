<?php

namespace App\Http\Controllers;

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

        // Calculate total points (this wasn't in the original code)
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
            'totalGroups',
            'totalGirls',
            'totalPlatforms',
            'shifts',
            'selectedShift',
            'dailyGroupData',
            'dailyTotalData',
            'latestPointsDate', // Añadir esta nueva variable
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

    private function operatorDashboard($user)
    {
        $currentShift = $this->getCurrentShift();
        $groupOperator = GroupOperator::where('user_id', $user->id)
            ->where('shift', $currentShift)
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
            'isOnBreak',
            'assignedShift'
        ));
    }
    private function getCurrentShift()
    {
        // Asumimos que el usuario actual es el operador
        $user = Auth::user();

        // Obtenemos el GroupOperator actual para el usuario
        $groupOperator = GroupOperator::where('user_id', $user->id)
            ->first();

        if ($groupOperator) {
            return $groupOperator->shift;
        }

        // Si no hay una asignación para hoy, podríamos buscar la más reciente
        $latestGroupOperator = GroupOperator::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();

        if ($latestGroupOperator) {
            return $latestGroupOperator->shift;
        }

        // Si no hay asignación, podríamos retornar un valor por defecto o null
        return null; // o 'unassigned' o cualquier otro valor por defecto que prefieras
    }
}
