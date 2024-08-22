<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;
use App\Models\Group;
use App\Models\GroupOperator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OperatorController extends Controller
{
    public function myPoints(Request $request)
    {
        $user = auth()->user();
        $viewType = $request->input('view', 'operator');

        if ($viewType === 'operator') {
            $data = $this->getOperatorData($user);
        } elseif ($viewType === 'group') {
            $data = $this->getGroupData($user);
        } else {
            $data = $this->getEmptyData();
        }

        $data['viewType'] = $viewType;
        $data['chartData'] = $this->prepareChartData($data['points'], $viewType);

        // Asegúrate de que todas las variables necesarias estén definidas
        $data['dailyGoal'] = $data['dailyGoal'] ?? 100; // Valor por defecto si no está definido
        $data['recentPoints'] = $data['points']->take(10);

        return view('operator.my_points', $data);
    }

    private function getOperatorData($user)
    {
        $points = $user->points()->orderBy('date', 'desc')->get();

        return [
            'points' => $points,
            'totalPoints' => $points->sum('points'),
            'monthlyPoints' => $points->where('date', '>=', now()->startOfMonth())->sum('points'),
            'weeklyPoints' => $points->where('date', '>=', now()->startOfWeek())->sum('points'),
            'todayPoints' => $points->where('date', now()->toDateString())->sum('points'),
            'monthlyPercentage' => $this->calculatePercentageChange($points, 'month'),
            'weeklyPercentage' => $this->calculatePercentageChange($points, 'week'),
            'dailyPercentage' => $this->calculatePercentageChange($points, 'day'),
            'bestDay' => $points->max('points'),
            'bestDayDate' => $points->where('points', $points->max('points'))->first()->date ?? null,
            'bestMonth' => $this->getBestMonth($points),
            'bestMonthDate' => $this->getBestMonthDate($points),
            'averagePoints' => $points->average('points'),
            'dailyGoal' => 100, // Ajusta este valor según sea necesario
        ];
    }

    private function getGroupData($user)
    {
        $now = Carbon::now();
        $yesterday = Carbon::yesterday();
        $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();
        $thisWeekStart = $now->copy()->startOfWeek();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $thisMonthStart = $now->copy()->startOfMonth();

        $currentGroupOperator = $user->groupOperators()
            ->where('date', '<=', $now)
            ->orderBy('date', 'desc')
            ->first();

        if (!$currentGroupOperator) {
            return $this->getEmptyData();
        }

        $group = $currentGroupOperator->group;

        // Get all user IDs associated with this group
        $groupUserIds = $group->operators->pluck('id')->toArray();

        // Query points for all users in the group
        $points = Point::whereIn('user_id', $groupUserIds)->orderBy('date', 'desc')->get();

        $data = $this->calculatePointsData($points, $now, $yesterday, $thisWeekStart, $lastWeekStart, $lastWeekEnd, $thisMonthStart, $lastMonthStart, $lastMonthEnd);
        $data['points'] = $points;

        // Additional group statistics
        $bestDayData = $points->groupBy('date')->map(function ($group) {
            return [
                'date' => $group->first()->date,
                'points' => $group->sum('points')
            ];
        })->sortByDesc('points')->first();

        $data['bestDay'] = $bestDayData['points'];
        $data['bestDayDate'] = $bestDayData['date'];

        $bestMonthData = $points->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('Y-m');
        })->map(function ($group) {
            $firstDate = Carbon::parse($group->first()->date);
            return [
                'month' => $firstDate->format('Y-m'),
                'points' => $group->sum('points')
            ];
        })->sortByDesc('points')->first();

        $data['bestMonth'] = $bestMonthData['points'];
        $data['bestMonthDate'] = $bestMonthData['month'];

        $data['averagePoints'] = $points->average('points');

        return $data;
    }

    private function calculatePointsData($points, $now, $yesterday, $thisWeekStart, $lastWeekStart, $lastWeekEnd, $thisMonthStart, $lastMonthStart, $lastMonthEnd)
    {
        $data = [
            'totalPoints' => $points->sum('points'),
            'monthlyPoints' => $points->where('date', '>=', $thisMonthStart->toDateString())->sum('points'),
            'lastMonthPoints' => $points->whereBetween('date', [$lastMonthStart->toDateString(), $lastMonthEnd->toDateString()])->sum('points'),
            'weeklyPoints' => $points->where('date', '>=', $thisWeekStart->toDateString())->sum('points'),
            'lastWeekPoints' => $points->whereBetween('date', [$lastWeekStart->toDateString(), $lastWeekEnd->toDateString()])->sum('points'),
            'todayPoints' => $points->where('date', $now->toDateString())->sum('points'),
            'yesterdayPoints' => $points->where('date', $yesterday->toDateString())->sum('points'),
            'recentPoints' => $points->take(10),
        ];

        $data['monthlyPercentage'] = $this->calculatePercentage($data['monthlyPoints'], $data['lastMonthPoints']);
        $data['weeklyPercentage'] = $this->calculatePercentage($data['weeklyPoints'], $data['lastWeekPoints']);
        $data['dailyPercentage'] = $this->calculatePercentage($data['todayPoints'], $data['yesterdayPoints']);

        return $data;
    }

    private function getEmptyData()
    {
        return [
            'points' => collect(),
            'totalPoints' => 0,
            'monthlyPoints' => 0,
            'weeklyPoints' => 0,
            'todayPoints' => 0,
            'monthlyPercentage' => 0,
            'weeklyPercentage' => 0,
            'dailyPercentage' => 0,
            'bestDay' => 0,
            'bestDayDate' => null,
            'bestMonth' => 0,
            'bestMonthDate' => null,
            'averagePoints' => 0,
            'dailyGoal' => 100,
        ];
    }


    private function calculatePercentageChange($points, $period)
    {
        $now = now();
        $currentPeriodStart = $now->copy()->startOf($period);
        $lastPeriodStart = $now->copy()->subUnit($period, 1)->startOf($period);
        $lastPeriodEnd = $lastPeriodStart->copy()->endOf($period);

        $currentPeriodPoints = $points->where('date', '>=', $currentPeriodStart)->sum('points');
        $lastPeriodPoints = $points->whereBetween('date', [$lastPeriodStart, $lastPeriodEnd])->sum('points');

        return $lastPeriodPoints > 0
            ? round((($currentPeriodPoints - $lastPeriodPoints) / $lastPeriodPoints) * 100, 2)
            : 100;
    }

    private function subUnit($period, $amount)
    {
        switch ($period) {
            case 'day':
                return $this->subDays($amount);
            case 'week':
                return $this->subWeeks($amount);
            case 'month':
                return $this->subMonths($amount);
            default:
                throw new \InvalidArgumentException("Invalid period: {$period}");
        }
    }
    private function getBestMonth($points)
    {
        return $points->groupBy(function ($point) {
            return Carbon::parse($point->date)->format('Y-m');
        })->map->sum('points')->max();
    }
    private function getBestMonthDate($points)
    {
        return $points->groupBy(function ($point) {
            return Carbon::parse($point->date)->format('Y-m');
        })->map->sum('points')->sortDesc()->keys()->first();
    }
    private function prepareChartData($points, $viewType)
    {
        return [
            'labels' => $points->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            }),
            'data' => $points->pluck('points'),
        ];
    }
}
