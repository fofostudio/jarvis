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
use App\Models\ScheduleDay;
use App\Services\ScheduleManager;




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
        $isInvertedShift = $request->boolean('is_inverted_shift');
        $isOptionalWork = $request->boolean('is_optional_work');
        $isNotScheduled = $request->boolean('is_not_scheduled');

        $sessionLog = SessionLog::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            [
                'status' => $newStatus,
                'is_inverted_shift' => $isInvertedShift,
                'is_optional_work' => $isOptionalWork,
                'is_not_scheduled' => $isNotScheduled
            ]
        );

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
        $today = Carbon::today();

        foreach ($operators as $operator) {
            Log::info("Processing operator: {$operator->name} (ID: {$operator->id})");

            $operatorAttendance = [
                'name' => $operator->name,
                'attendance' => [],
            ];

            foreach ($weekPeriod as $date) {
                $formattedDate = $date->toDateString();
                Log::info("Checking date for operator {$operator->id}: {$formattedDate}");

                $scheduleDay = ScheduleDay::where('date', $formattedDate)->first();
                $isInverted = $scheduleDay ? $scheduleDay->is_inverted : false;
                $mandatoryShift = $scheduleDay ? $scheduleDay->mandatory_shift : null;

                $operatorShift = $operator->groupOperators->first()->shift;
                $actualShift = $this->getActualShift($operatorShift, $isInverted, $mandatoryShift);

                $log = SessionLog::where('user_id', $operator->id)
                    ->whereDate('date', $formattedDate)
                    ->first();

                if ($date->lte($today)) {
                    if ($log) {
                        Log::info("Log found for date {$formattedDate}");

                        if ($log->status && $log->status !== 'pending') {
                            $status = $log->status;
                        } else {
                            $shiftStartTime = $this->getShiftStartTime($actualShift);
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
                } else {
                    $status = 'pending';
                }

                $operatorAttendance['attendance'][$formattedDate] = $status;
                Log::info("Status set for {$formattedDate}: {$status}");
            }

            $weeklyAttendance[$operator->id] = $operatorAttendance;
        }

        return $weeklyAttendance;
    }
    private function getActualShift($operatorShift, $isInverted, $mandatoryShift)
    {
        if ($mandatoryShift) {
            return $mandatoryShift;
        }

        if ($isInverted) {
            return $this->getInvertedShift($operatorShift);
        }

        return $operatorShift;
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
    private function getInvertedShift($shift)
    {
        $invertedShifts = [
            'morning' => 'afternoon',
            'afternoon' => 'morning',
            'night' => 'night',
            'complete' => 'complete',
        ];

        return $invertedShifts[$shift] ?? $shift;
    }
    public function getSessionLogData($userId, $date)
    {
        $sessionLog = SessionLog::where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();

        $data = [
            'registeredTime' => $sessionLog ? $sessionLog->first_login?->format('H:i:s') : null,
            'isInvertedShift' => $sessionLog ? $sessionLog->is_inverted_shift : false,
            'isOptionalWork' => $sessionLog ? $sessionLog->is_optional_work : false,
            'isNotScheduled' => $sessionLog ? $sessionLog->is_not_scheduled : false,
            'status' => $sessionLog ? $sessionLog->status : null,
        ];

        return response()->json($data);
    }
    public function getSessionLogTime($userId, $date)
    {
        $sessionLog = SessionLog::where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();

        $registeredTime = $sessionLog ? $sessionLog->first_login->format('H:i:s') : null;

        return response()->json(['registeredTime' => $registeredTime]);
    }

    private function getShiftStartTime($shift)
    {
        switch ($shift) {
            case 'morning':
                return Carbon::createFromTime(6, 11, 0);
            case 'afternoon':
                return Carbon::createFromTime(14, 11, 0);
            case 'night':
                return Carbon::createFromTime(22, 11, 0);
            case 'complete':
                return Carbon::createFromTime(10, 11, 0);
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

        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : $endDate->copy()->startOfMonth();

        $groupOperator = GroupOperator::where('user_id', $user->id)->first();
        if (!$groupOperator) {
            return view('admin.session_logs.my_logins', compact('startDate', 'endDate'))
                ->withErrors(['message' => 'No tienes una jornada asignada.']);
        }

        $operatorShift = $groupOperator->shift;
        $calendarEvents = [];
        $indicators = [
            'on_time' => 0,
            'late' => 0,
            'absent' => 0,
            'justified_absence' => 0,
            'suspension' => 0,
            'remote' => 0,
            'late_recovery' => 0,
            'absence_recovery' => 0,
            'pending' => 0,
            'totalBreakTime' => 0,
            'totalWorkHours' => 0,
        ];

        $sessionLogs = SessionLog::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        $breakLogs = BreakLog::where('user_id', $user->id)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time', 'asc')
            ->get();

        $currentDate = $startDate->copy();
        $today = Carbon::today();

        while ($currentDate <= $endDate) {
            $formattedDate = $currentDate->format('Y-m-d');
            $log = $sessionLogs->get($formattedDate);
            $scheduleDay = ScheduleDay::whereDate('date', $formattedDate)->first();

            $isInverted = $scheduleDay ? $scheduleDay->is_inverted : false;
            $mandatoryShift = $scheduleDay ? $scheduleDay->mandatory_shift : null;
            $actualShift = $this->getActualShift($operatorShift, $isInverted, $mandatoryShift);
            $shiftStartTime = $this->getShiftStartTime($actualShift);

            if ($log) {
                if ($log->status && $log->status !== 'pending') {
                    $status = $log->status;
                } else {
                    if ($log->first_login) {
                        $loginTime = Carbon::parse($log->first_login);
                        $status = $loginTime->gt($shiftStartTime) ? 'late' : 'on_time';

                        if ($log->last_logout) {
                            $workHours = Carbon::parse($log->last_logout)->diffInHours($loginTime);
                            $indicators['totalWorkHours'] += $workHours;
                        }
                    } else {
                        $status = 'absent';
                    }
                }
            } elseif ($currentDate->lt($today)) {
                $status = 'absent';
            } else {
                $status = 'pending';
            }

            $indicators[$status]++;

            $calendarEvents[] = $this->createCalendarEvent($formattedDate, $status, $isInverted);

            $currentDate->addDay();
        }

        $indicators['totalBreakTime'] = $breakLogs->sum('overtime');
        $indicators['averageWorkHours'] = $sessionLogs->count() > 0
            ? $indicators['totalWorkHours'] / $sessionLogs->count()
            : 0;

        return view('admin.session_logs.my_logins', compact(
            'sessionLogs',
            'breakLogs',
            'calendarEvents',
            'indicators',
            'startDate',
            'endDate'
        ));
    }

    private function createCalendarEvent($date, $status, $isInverted)
    {
        $color = $this->getStatusColor($status);
        $title = $this->getStatusTitle($status);
        if ($isInverted) {
            $title .= ' (Invertido)';
        }
        return [
            'title' => $title,
            'start' => $date,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'allDay' => true
        ];
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
            case 'justified_absence':
                return '#007bff'; // azul
            case 'suspension':
                return '#fd7e14'; // naranja
            case 'remote':
                return '#6f42c1'; // morado
            case 'late_recovery':
                return '#20c997'; // turquesa
            case 'absence_recovery':
                return '#17a2b8'; // cian
            default:
                return '#6c757d'; // gris para cualquier otro estado
        }
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
            case 'justified_absence':
                return 'Falla Justificada';
            case 'suspension':
                return 'Suspensión';
            case 'remote':
                return 'Remoto';
            case 'late_recovery':
                return 'Rec Retardo';
            case 'absence_recovery':
                return 'Rec Falla';
            default:
                return ucfirst($status);
        }
    }
}
