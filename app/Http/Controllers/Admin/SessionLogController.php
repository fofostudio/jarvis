<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BreakLog;
use App\Models\GroupOperator;
use App\Models\SessionLog;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class SessionLogController extends Controller
{
    public function index(Request $request)
    {
        // Determinar la semana
        if ($request->has('week')) {
            [$year, $week] = explode('-', $request->input('week'));
            $currentWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
        } else {
            $currentWeek = Carbon::now()->startOfWeek();
        }
        $endOfWeek = $currentWeek->copy()->endOfWeek();

        // Crear un período para la semana
        $weekPeriod = CarbonPeriod::create($currentWeek, $endOfWeek);

        Log::info("Fetching attendance for week: " . $currentWeek->toDateString() . " to " . $endOfWeek->toDateString());

        $shifts = ['morning', 'afternoon', 'night', 'complete'];
        $attendanceData = [];
        $allTimeAttendanceData = [];

        foreach ($shifts as $shift) {
            $operators = User::whereIn('role', ['Operator', 'Admin'])
                ->whereHas('groupOperators', function ($query) use ($shift) {
                    $query->where('shift', $shift);
                })
                ->with(['groupOperators' => function ($query) use ($shift) {
                    $query->where('shift', $shift);
                }])
                ->get();

            $attendanceData[$shift] = $this->getWeeklyAttendance($operators, $weekPeriod);
            $allTimeAttendanceData[$shift] = $this->getAllTimeAttendance($operators);
        }

        $statistics = $this->getStatistics($allTimeAttendanceData);

        $prevWeek = $currentWeek->copy()->subWeek();
        $nextWeek = $currentWeek->copy()->addWeek();
        $currentWeekDates = $weekPeriod->toArray();

        return view('admin.session_logs.index', compact('currentWeek', 'prevWeek', 'nextWeek', 'currentWeekDates', 'shifts', 'attendanceData', 'statistics'));
    }
    public function updateAttendanceStatus(Request $request)
    {
        $userId = $request->input('user_id');
        $date = $request->input('date');
        $newStatus = $request->input('status');

        $sessionLog = SessionLog::where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();

        if (!$sessionLog) {
            $sessionLog = new SessionLog();
            $sessionLog->user_id = $userId;
            $sessionLog->date = $date;
        }

        $sessionLog->status = $newStatus;
        $sessionLog->save();

        return response()->json(['success' => true]);
    }
    private function getAllTimeAttendance($operators)
    {
        $allTimeAttendance = [];

        foreach ($operators as $operator) {
            $logs = SessionLog::where('user_id', $operator->id)->get();

            $allTimeAttendance[$operator->id] = [
                'name' => $operator->name,
                'attendance' => [
                    'on_time' => 0,
                    'late' => 0,
                    'absent' => 0,
                    'present' => 0
                ]
            ];

            foreach ($logs as $log) {
                $shiftStartTime = $this->getShiftStartTime($operator->groupOperators->first()->shift);

                if (!$log->first_login) {
                    $allTimeAttendance[$operator->id]['attendance']['absent']++;
                } elseif ($log->first_login && Carbon::parse($log->first_login)->format('H:i:s') <= $shiftStartTime->format('H:i:s')) {
                    $allTimeAttendance[$operator->id]['attendance']['on_time']++;
                } else {
                    $allTimeAttendance[$operator->id]['attendance']['late']++;
                }
            }
        }

        return $allTimeAttendance;
    }

    private function getWeeklyAttendance($operators, CarbonPeriod $weekPeriod)
    {
        $weeklyAttendance = [];

        foreach ($operators as $operator) {
            Log::info("Processing operator: {$operator->name} (ID: {$operator->id})");

            $operatorAttendance = [
                'name' => $operator->name,
                'attendance' => [],
            ];

            foreach ($weekPeriod as $date) {
                $formattedDate = $date->toDateString();
                Log::info("Checking date for operator {$operator->id}: {$formattedDate}");

                $log = SessionLog::where('user_id', $operator->id)
                    ->whereDate('date', $formattedDate)
                    ->first();

                if ($log) {
                    Log::info("Log found for date {$formattedDate}");

                    // Primero, verificamos si hay un estado en la columna 'status'
                    if ($log->status && $log->status !== 'pending') {
                        $status = $log->status;
                    } else {
                        // Si no hay un estado válido, calculamos el estado basado en la lógica existente
                        $shiftStartTime = $this->getShiftStartTime($operator->groupOperators->first()->shift);
                        $loginDateTime = $log->first_login ? Carbon::parse($log->first_login) : null;

                        if ($loginDateTime && $loginDateTime->format('H:i:s') <= $shiftStartTime->format('H:i:s')) {
                            $status = 'on_time';
                        } elseif ($loginDateTime) {
                            $status = 'late';
                        } else {
                            $status = 'absent';
                        }
                    }
                } else {
                    Log::info("No log found for date {$formattedDate}");
                    $status = 'absent';
                }

                $operatorAttendance['attendance'][$formattedDate] = $status;
                Log::info("Status set for {$formattedDate}: {$status}");
            }

            $weeklyAttendance[$operator->id] = $operatorAttendance;
        }

        return $weeklyAttendance;
    }
    private function getStatistics($allTimeAttendanceData)
    {
        $statistics = [
            'top_attendance' => [],
            'top_absence' => [],
            'top_late' => [],
        ];

        foreach ($allTimeAttendanceData as $shiftData) {
            foreach ($shiftData as $operatorId => $operatorData) {
                $statistics['top_attendance'][] = [
                    'name' => $operatorData['name'],
                    'count' => $operatorData['attendance']['on_time']
                ];
                $statistics['top_absence'][] = [
                    'name' => $operatorData['name'],
                    'count' => $operatorData['attendance']['absent']
                ];
                $statistics['top_late'][] = [
                    'name' => $operatorData['name'],
                    'count' => $operatorData['attendance']['late']
                ];
            }
        }

        foreach ($statistics as &$stat) {
            usort($stat, function ($a, $b) {
                return $b['count'] - $a['count'];
            });
            $stat = array_slice($stat, 0, 5);
        }

        return $statistics;
    }
    private function getShiftStartTime($shift)
    {
        switch ($shift) {
            case 'morning':
                return Carbon::createFromTime(6, 10, 0);
            case 'afternoon':
                return Carbon::createFromTime(14, 10, 0);
            case 'night':
                return Carbon::createFromTime(22, 10, 0);
            case 'complete':
                return Carbon::createFromTime(10, 10, 0);
            default:
                return null;
        }
    }
    public function closeOperatorSession(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $currentDate = Carbon::now()->toDateString();

            $sessionLog = SessionLog::where('user_id', $userId)
                ->whereDate('date', $currentDate)
                ->whereNull('last_logout')
                ->first();

            if ($sessionLog) {
                $sessionLog->last_logout = Carbon::now();
                $sessionLog->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Sesión cerrada correctamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró una sesión abierta para este usuario en la fecha actual'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar la sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myLogins(Request $request)
    {
        $user = Auth::user();

        // Determinar el rango de fechas (último mes por defecto)
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : $endDate->copy()->startOfMonth();

        $sessionLogs = SessionLog::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('date', 'asc')
            ->get();

        $breakLogs = BreakLog::where('user_id', $user->id)
            ->whereBetween('start_time', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('start_time', 'asc')
            ->get();

        $calendarEvents = [];
        $indicators = [
            'onTimeCount' => 0,
            'lateCount' => 0,
            'absentCount' => 0,
            'totalBreakTime' => 0,
            'totalWorkHours' => 0,
        ];

        foreach ($sessionLogs as $log) {
            $groupOperator = GroupOperator::where('user_id', $user->id)->first();
            if (!$groupOperator) {
                // Manejar el caso en que el usuario no tiene una jornada asignada
                continue;
            }

            $shift = $groupOperator->shift;
            $shiftStartTime = $this->getShiftStartTime($shift);
            if ($log->first_login) {
                $loginTime = Carbon::parse($log->first_login);
                if ($loginTime <= $shiftStartTime) {
                    $status = 'on_time';
                    $indicators['onTimeCount']++;
                } else {
                    $status = 'late';
                    $indicators['lateCount']++;
                }

                if ($log->last_logout) {
                    $workHours = Carbon::parse($log->last_logout)->diffInHours($loginTime);
                    $indicators['totalWorkHours'] += $workHours;
                }
            } else {
                $status = 'absent';
                $indicators['absentCount']++;
            }
            $calendarEvents[] = $this->createCalendarEvent($log->date, $status);
        }

        $indicators['totalBreakTime'] = $breakLogs->sum('overtime');
        $indicators['averageWorkHours'] = $sessionLogs->count() > 0
            ? $indicators['totalWorkHours'] / $sessionLogs->count()
            : 0;

        return view('admin.session_logs.my_logins', compact(
            'sessionLogs',
            'breakLogs',
            'indicators',
            'calendarEvents',
            'startDate',
            'endDate'
        ));
    }
    private function createCalendarEvent($date, $status)
    {
        $color = $this->getStatusColor($status);
        $title = $this->getStatusTitle($status);
        return [
            'title' => $title,
            'start' => $date,
            'allDay' => true,  // Esto hace que el evento sea "todo el día"
            'backgroundColor' => $color,
            'borderColor' => $color,
        ];
    }
    private function getStatusTitle($status)
    {
        switch ($status) {
            case 'on_time':
                return 'A Tiempo';
            case 'late':
                return 'Llegó Tarde';
            case 'absent':
                return 'Ausente';
            default:
                return ucfirst($status);
        }
    }
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'on_time':
                return '#28a745'; // verde
            case 'late':
                return '#ffc107'; // amarillo
            case 'absent':
                return '#dc3545'; // rojo
            default:
                return '#6c757d'; // gris para cualquier otro estado
        }
    }
}
