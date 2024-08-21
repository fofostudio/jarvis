<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SessionLog::with('user');

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $sessionLogs = $query->orderBy('date', 'desc')->paginate(15);
        $users = User::all();

        return view('admin.session_logs.index', compact('sessionLogs', 'users'));
    }

    public function myLogins(Request $request)
    {
        $user = Auth::user();
        $sessionLogs = $user->sessionLogs()->orderBy('date', 'desc')->get();

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

            // Determine shift times
            $shiftStartTime = $this->getShiftStartTime($log->shift);
            $shiftEndTime = $this->getShiftEndTime($log->shift);

            // Check attendance
            if ($log->login_time) {
                $loginTime = Carbon::parse($log->login_time);
                if ($loginTime <= $shiftStartTime) {
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

            // Calculate break time
            $breakTime = $log->break_duration ?? 0;
            $data['breakData'][] = $breakTime / 60; // Convert to hours
            $indicators['totalBreakTime'] += $breakTime;

            // Calculate work hours
            if ($log->login_time && $log->logout_time) {
                $workHours = Carbon::parse($log->logout_time)->diffInHours(Carbon::parse($log->login_time));
                $data['workHoursData'][] = $workHours;
                $totalWorkHours += $workHours;
            } else {
                $data['workHoursData'][] = 0;
            }
        }

        $sessionsCount = count($sessionLogs);
        $indicators['averageWorkHours'] = $sessionsCount > 0 ? $totalWorkHours / $sessionsCount : 0;

        return view('admin.session_logs.my_logins', compact('sessionLogs', 'data', 'indicators'));
    }

    private function getShiftStartTime($shift)
    {
        switch ($shift) {
            case 'morning':
                return Carbon::createFromTime(6, 0, 0);
            case 'afternoon':
                return Carbon::createFromTime(14, 0, 0);
            case 'night':
                return Carbon::createFromTime(22, 0, 0);
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
