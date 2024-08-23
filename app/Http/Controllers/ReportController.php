<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;
use App\Models\User;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFilter = $request->input('date_filter', 'this_month');
        $dateRange = $this->getDateRange($dateFilter);

        // Depuración del rango de fechas
        $debug = [
            'dateFilter' => $dateFilter,
            'startDate' => $dateRange[0]->toDateTimeString(),
            'endDate' => $dateRange[1]->toDateTimeString(),
        ];

        $userPointsQuery = User::where('role', 'operator')
            ->withSum(['points as total_points' => function ($query) use ($dateRange) {
                $query->whereBetween('date', $dateRange);
            }], 'points')
            ->withAvg(['points as average_points' => function ($query) use ($dateRange) {
                $query->whereBetween('date', $dateRange);
            }], 'points')
            ->withCount(['points as achieved_goals' => function ($query) use ($dateRange) {
                $query->whereBetween('date', $dateRange)->whereRaw('points >= goal');
            }])
            ->withCount(['points as total_goals' => function ($query) use ($dateRange) {
                $query->whereBetween('date', $dateRange);
            }])
            ->orderBy('name', 'asc'); // Add this line to sort users alphabetically

        $debug['userPointsQuery'] = $userPointsQuery->toSql();
        $debug['userPointsQueryBindings'] = $userPointsQuery->getBindings();

        $userPoints = $userPointsQuery->get()
            ->map(function ($user) {
                $user->total_points = round($user->total_points ?? 0, 2);
                $user->average_points = round($user->average_points ?? 0, 2);
                return $user;
            });

        $groupPointsQuery = Group::withSum(['points as total_points' => function ($query) use ($dateRange) {
            $query->whereBetween('date', $dateRange);
        }], 'points')
            ->withAvg(['points as average_points' => function ($query) use ($dateRange) {
                $query->whereBetween('date', $dateRange);
            }], 'points')
            ->withCount(['points as achieved_goals' => function ($query) use ($dateRange) {
                $query->whereBetween('date', $dateRange)->whereRaw('points >= goal');
            }])
            ->withCount(['points as total_goals' => function ($query) use ($dateRange) {
                $query->whereBetween('date', $dateRange);
            }])
            ->orderBy('name', 'asc'); // Add this line to sort groups alphabetically

        $debug['groupPointsQuery'] = $groupPointsQuery->toSql();
        $debug['groupPointsQueryBindings'] = $groupPointsQuery->getBindings();

        $groupPoints = $groupPointsQuery->get()
            ->map(function ($group) {
                $group->total_points = round($group->total_points ?? 0, 2);
                $group->average_points = round($group->average_points ?? 0, 2);
                return $group;
            });

        $debug['rawPoints'] = Point::whereBetween('date', $dateRange)->get();

        return view('reports.index', compact('userPoints', 'groupPoints', 'dateFilter', 'debug'));
    }

    private function getDateRange($filter)
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'this_month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
            case 'last_month':
                return [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()];
            case 'this_year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear()];
            case 'last_year':
                return [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()];
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            default:
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
        }
    }
}
