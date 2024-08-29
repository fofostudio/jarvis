<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupOperator;
use App\Models\SessionLog;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionLogController extends Controller
{
    public function index(Request $request)
    {
        $currentWeek = Carbon::now();

        if ($request->has('week')) {
            [$year, $week] = explode('-', $request->input('week'));
            $currentWeek->setISODate($year, $week);
        }

        $prevWeek = $currentWeek->copy()->subWeek();
        $nextWeek = $currentWeek->copy()->addWeek();

        $startDate = $currentWeek->startOfWeek();
        $endDate = $currentWeek->endOfWeek();

        $currentWeekDates = collect(new DatePeriod($startDate, new DateInterval('P1D'), $endDate->addDay()))->map(function ($date) {
            return $date;
        });

        $shifts = ['morning', 'afternoon', 'night'];
        $attendanceData = [];

        foreach ($shifts as $shift) {
            $operators = User::where('role', 'Operator')
                ->whereHas('groupOperators', function ($query) use ($shift) {
                    $query->where('shift', $shift);
                })
                ->with(['groupOperators' => function ($query) use ($shift) {
                    $query->where('shift', $shift);
                }])
                ->get();

            $attendanceData[$shift] = $this->getAttendanceData($operators, $startDate, $endDate);
        }

        $statistics = $this->getStatistics(collect($attendanceData)->flatten(1)->toArray());

        return view('admin.session_logs.index', compact('currentWeek', 'prevWeek', 'nextWeek', 'currentWeekDates', 'shifts', 'attendanceData', 'statistics'));
    }

    private function generateDateRange($startDate, $endDate)
    {
        $dates = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        return $dates;
    }

    private function getAttendanceData($operators, $startDate, $endDate)
    {
        $attendanceData = [];

        foreach ($operators as $operator) {
            $userLogs = SessionLog::where('user_id', $operator->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $attendanceData[$operator->id] = [
                'name' => $operator->name,
                'attendance' => [],
            ];

            foreach ($this->generateDateRange($startDate, $endDate) as $date) {
                $log = $userLogs->firstWhere('date', $date);
                if (!$log) {
                    $attendanceData[$operator->id]['attendance'][$date] = 'absent';
                } elseif ($log->first_login && $log->first_login <= $this->getShiftStartTime($operator->groupOperators->first()->shift)) {
                    $attendanceData[$operator->id]['attendance'][$date] = 'on_time';
                } else {
                    $attendanceData[$operator->id]['attendance'][$date] = 'late';
                }
            }
        }

        return $attendanceData;
    }

    private function getStatistics($attendanceData)
    {
        $statistics = [
            'top_attendance' => [],
            'top_absence' => [],
            'top_late' => [],
        ];

        foreach ($attendanceData as $userId => $userData) {
            $attendanceCount = collect($userData['attendance'])->count(function ($status) {
                return $status === 'on_time';
            });
            $absenceCount = collect($userData['attendance'])->count(function ($status) {
                return $status === 'absent';
            });
            $lateCount = collect($userData['attendance'])->count(function ($status) {
                return $status === 'late';
            });

            $statistics['top_attendance'][] = ['name' => $userData['name'], 'count' => $attendanceCount];
            $statistics['top_absence'][] = ['name' => $userData['name'], 'count' => $absenceCount];
            $statistics['top_late'][] = ['name' => $userData['name'], 'count' => $lateCount];
        }

        foreach ($statistics as &$stat) {
            rsort($stat);
            $stat = array_slice($stat, 0, 5);
        }

        return $statistics;
    }


    public function myLogins(Request $request)
    {
        $user = Auth::user();
        $sessionLogs = $user->sessionLogs()->orderBy('date', 'desc')->get();
        $breakLogs = $user->breakLogs()->orderBy('start_time', 'desc')->get();

        $data = [
            'labels' => [],
            'attendanceData' => [],
            'breakData' => [],
            'workHoursData' => [],
        ];

        $indicators = [
            'onTimeCount' => 0,
            'lateCount' => 0,
            'absentCount' => 0,
            'totalBreakTime' => 0,
            'averageWorkHours' => 0,
        ];

        $totalWorkHours = 0;

        foreach ($sessionLogs as $log) {
            $date = $log->date->format('Y-m-d');
            $data['labels'][] = $date;

            // Check attendance
            if ($log->first_login) {
                $loginTime = Carbon::parse($log->first_login);
                if ($loginTime <= $this->getShiftStartTime($log->shift)) {
                    $data['attendanceData'][] = 1;
                    $indicators['onTimeCount']++;
                } else {
                    $data['attendanceData'][] = 0.5;
                    $indicators['lateCount']++;
                }
            } else {
                $data['attendanceData'][] = 0;
                $indicators['absentCount']++;
            }

            // Calculate work hours
            if ($log->first_login && $log->last_logout) {
                $workHours = Carbon::parse($log->last_logout)->diffInHours(Carbon::parse($log->first_login));
                $data['workHoursData'][] = $workHours;
                $totalWorkHours += $workHours;
            } else {
                $data['workHoursData'][] = 0;
            }
        }

        foreach ($breakLogs as $log) {
            $date = $log->start_time->format('Y-m-d');

            // Calculate break time
            $breakTime = $log->overtime > 0 ? $log->overtime : 0;
            $indicators['totalBreakTime'] += $breakTime;
        }

        $sessionsCount = count($sessionLogs);
        $indicators['averageWorkHours'] = $sessionsCount > 0 ? $totalWorkHours / $sessionsCount : 0;

        return view('admin.session_logs.my_logins', compact('sessionLogs', 'breakLogs', 'data', 'indicators'));
    }

    private function getShiftStartTime($shift)
    {
        switch ($shift) {
            case 'morning':
                return Carbon::createFromTime(6, 15, 0);
            case 'afternoon':
                return Carbon::createFromTime(14, 15, 0);
            case 'night':
                return Carbon::createFromTime(22, 15, 0);
            default:
                return null;
        }
    }

    private function getShiftEndTime($shift)
    {
        switch ($shift) {
            case 'morning':
                return Carbon::createFromTime(14, 0, 0);
            case 'afternoon':
                return Carbon::createFromTime(22, 0, 0);
            case 'night':
                return Carbon::createFromTime(6, 0, 0)->addDay();
            default:
                return null;
        }
    }
}
