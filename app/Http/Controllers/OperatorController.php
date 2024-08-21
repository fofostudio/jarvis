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

        return view('operator.my_points', $data);
    }

    private function getOperatorData($user)
    {
        $now = Carbon::now();
        $yesterday = Carbon::yesterday();
        $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();
        $thisWeekStart = $now->copy()->startOfWeek();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $thisMonthStart = $now->copy()->startOfMonth();

        $points = $user->points()->orderBy('date', 'desc')->get();

        $data = $this->calculatePointsData($points, $now, $yesterday, $thisWeekStart, $lastWeekStart, $lastWeekEnd, $thisMonthStart, $lastMonthStart, $lastMonthEnd);
        $data['points'] = $points;

        // Additional operator statistics
        $bestDayData = $points->groupBy('date')->map(function ($group) {
            return [
                'date' => $group->first()->date,
                'points' => $group->sum('points')
            ];
        })->sortByDesc('points')->first();

        $data['bestDay'] = $bestDayData['points'];
        $data['bestDayDate'] = $bestDayData['date'];

        $bestMonthData = $points->groupBy(function($item) {
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
        $data['totalPointsEver'] = $points->sum('points');
        $data['currentMonthPoints'] = $points->where('date', '>=', $thisMonthStart->toDateString())->sum('points');
        $data['lastMonthPoints'] = $points->whereBetween('date', [$lastMonthStart->toDateString(), $lastMonthEnd->toDateString()])->sum('points');
        $data['monthlyImprovement'] = $this->calculatePercentage($data['currentMonthPoints'], $data['lastMonthPoints']);
        $data['currentWeekPoints'] = $points->where('date', '>=', $thisWeekStart->toDateString())->sum('points');
        $data['lastWeekPoints'] = $points->whereBetween('date', [$lastWeekStart->toDateString(), $lastWeekEnd->toDateString()])->sum('points');
        $data['weeklyImprovement'] = $this->calculatePercentage($data['currentWeekPoints'], $data['lastWeekPoints']);
        return $data;
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

        $bestMonthData = $points->groupBy(function($item) {
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
            'totalPoints' => 0,
            'monthlyPoints' => 0,
            'lastMonthPoints' => 0,
            'weeklyPoints' => 0,
            'lastWeekPoints' => 0,
            'todayPoints' => 0,
            'yesterdayPoints' => 0,
            'recentPoints' => collect(),
            'points' => collect(),
            'monthlyPercentage' => 0,
            'weeklyPercentage' => 0,
            'dailyPercentage' => 0,
        ];
    }

    private function calculatePercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function prepareChartData($points, $viewType)
    {
        $chartData = $points->groupBy('date')
            ->map(function ($group) {
                return [
                    'date' => $group->first()->date,
                    'total' => $group->sum('points')
                ];
            })
            ->sortBy('date');

        return [
            'labels' => $chartData->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            }),
            'data' => $chartData->pluck('total')
        ];
    }
}
