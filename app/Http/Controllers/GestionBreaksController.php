<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BreakLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GestionBreaksController extends Controller
{
    public function index()
    {
        $fecha = request('fecha', Carbon::today()->toDateString());
        $estadisticas = $this->calcularEstadisticas($fecha);

        return view('admin.session_logs.break-management', compact('estadisticas', 'fecha'));
    }

    public function getDatosTabla(Request $request)
    {
        $fecha = $request->input('fecha', Carbon::today()->toDateString());

        $breaks = BreakLog::with('user')
            ->whereDate('start_time', $fecha)
            ->get()
            ->map(function ($break) {
                $duracion = $break->actual_end_time
                    ? $break->actual_end_time->diffInMinutes($break->start_time)
                    : Carbon::now()->diffInMinutes($break->start_time);

                return [
                    'id' => $break->id,
                    'usuario' => $break->user->name,
                    'inicio' => $break->start_time->format('H:i:s'),
                    'fin' => $break->actual_end_time ? $break->actual_end_time->format('H:i:s') : 'En progreso',
                    'duracion' => $duracion,
                    'overtime' => $break->overtime ? round($break->overtime / 60, 2) : 0,
                    'estado' => $break->isOngoing() ? 'En progreso' : 'Finalizado'
                ];
            });

        return response()->json($breaks);
    }

    private function getBreaksByDate($fecha)
    {
        return BreakLog::with('user')
            ->whereDate('start_time', $fecha)
            ->get()
            ->map(function ($break) {
                $duracion = $break->actual_end_time
                    ? $break->actual_end_time->diffInMinutes($break->start_time)
                    : Carbon::now()->diffInMinutes($break->start_time);

                return [
                    'id' => $break->id,
                    'usuario' => $break->user->name,
                    'inicio' => $break->start_time->format('H:i:s'),
                    'fin' => $break->actual_end_time ? $break->actual_end_time->format('H:i:s') : 'En progreso',
                    'duracion' => $duracion,
                    'overtime' => $break->overtime ? round($break->overtime / 60, 2) : 0,
                    'estado' => $break->isOngoing() ? 'En progreso' : 'Finalizado'
                ];
            });
    }

    private function calcularEstadisticas($fecha)
    {
        $breaks = BreakLog::whereDate('start_time', $fecha)->get();

        $masOvertime = $breaks->sortByDesc('overtime')->first();

        return [
            'total_breaks' => $breaks->count(),
            'promedio_duracion' => $breaks->avg(function ($break) {
                return $break->actual_end_time
                    ? $break->actual_end_time->diffInMinutes($break->start_time)
                    : Carbon::now()->diffInMinutes($break->start_time);
            }),
            'total_overtime' => $breaks->sum('overtime') / 60, // en minutos
            'mas_overtime' => $masOvertime ? [
                'usuario' => $masOvertime->user->name,
                'overtime' => round($masOvertime->overtime / 60, 2)
            ] : null,
        ];
    }

    public function iniciarBreak(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $breakExistente = BreakLog::where('user_id', $user->id)
            ->whereDate('start_time', Carbon::today())
            ->whereNull('actual_end_time')
            ->first();

        if ($breakExistente) {
            return response()->json(['error' => 'El usuario ya tiene un break activo.'], 400);
        }

        $breakLog = new BreakLog();
        $breakLog->user_id = $user->id;
        $breakLog->start_time = Carbon::now();
        $breakLog->expected_end_time = Carbon::now()->addMinutes(30);
        $breakLog->save();

        return response()->json(['mensaje' => 'Break iniciado exitosamente.', 'break' => $breakLog]);
    }

    public function finalizarBreak(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $breakLog = BreakLog::where('user_id', $user->id)
            ->whereDate('start_time', Carbon::today())
            ->whereNull('actual_end_time')
            ->first();

        if (!$breakLog) {
            return response()->json(['error' => 'El usuario no tiene un break activo para finalizar.'], 400);
        }

        $ahora = Carbon::now();
        $breakLog->actual_end_time = $ahora;

        $tiempoExtra = $ahora->diffInSeconds($breakLog->expected_end_time, false);
        if ($tiempoExtra > 0) {
            $breakLog->overtime = $tiempoExtra;
        }

        $breakLog->save();

        return response()->json(['mensaje' => 'Break finalizado exitosamente.', 'break' => $breakLog]);
    }

    public function obtenerEstadisticasBreaks()
    {
        $hoy = Carbon::today();
        $inicioSemana = $hoy->startOfWeek();
        $finSemana = $hoy->endOfWeek();

        $estadisticas = BreakLog::whereBetween('start_time', [$inicioSemana, $finSemana])
            ->selectRaw('
                COUNT(*) as total_breaks,
                AVG(TIMESTAMPDIFF(SECOND, start_time, IFNULL(actual_end_time, NOW()))) as duracion_promedio,
                SUM(overtime) as tiempo_extra_total
            ')
            ->first();

        return response()->json([
            'total_breaks' => $estadisticas->total_breaks,
            'duracion_promedio' => round($estadisticas->duracion_promedio / 60, 2), // en minutos
            'tiempo_extra_total' => round($estadisticas->tiempo_extra_total / 60, 2), // en minutos
        ]);
    }

    public function obtenerBreaksPorDia()
    {
        $hoy = Carbon::today();
        $inicioSemana = $hoy->startOfWeek();
        $finSemana = $hoy->endOfWeek();

        $breaksPorDia = BreakLog::whereBetween('start_time', [$inicioSemana, $finSemana])
            ->selectRaw('DATE(start_time) as fecha, COUNT(*) as total_breaks')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return response()->json($breaksPorDia);
    }

    public function obtenerTiempoExtraPorOperador()
    {
        $hoy = Carbon::today();
        $inicioSemana = $hoy->startOfWeek();
        $finSemana = $hoy->endOfWeek();

        $tiempoExtraPorOperador = BreakLog::whereBetween('start_time', [$inicioSemana, $finSemana])
            ->selectRaw('user_id, SUM(overtime) as tiempo_extra_total')
            ->groupBy('user_id')
            ->with('user:id,name')
            ->orderByDesc('tiempo_extra_total')
            ->get()
            ->map(function ($item) {
                return [
                    'nombre' => $item->user->name,
                    'tiempo_extra' => round($item->tiempo_extra_total / 60, 2) // en minutos
                ];
            });

        return response()->json($tiempoExtraPorOperador);
    }
    public function getDatosDashboard(Request $request)
    {
        $fecha = $request->input('fecha', Carbon::today()->toDateString());
        $estadisticas = $this->calcularEstadisticas($fecha);
        $chartData = $this->getChartData($fecha);

        return response()->json([
            'estadisticas' => $estadisticas,
            'chartData' => $chartData,
        ]);
    }

    private function getChartData($fecha)
    {
        // Implementa la lógica para obtener los datos de los gráficos
        // Esto es un ejemplo, ajusta según tus necesidades
        $breakDurationDistribution = BreakLog::whereDate('start_time', $fecha)
            ->selectRaw('FLOOR(TIMESTAMPDIFF(MINUTE, start_time, IFNULL(actual_end_time, NOW())) / 5) * 5 AS duration_group, COUNT(*) AS count')
            ->groupBy('duration_group')
            ->orderBy('duration_group')
            ->get();

        $overtimeTrend = BreakLog::whereDate('start_time', '>=', Carbon::parse($fecha)->subDays(30))
            ->whereDate('start_time', '<=', $fecha)
            ->selectRaw('DATE(start_time) AS date, SUM(overtime) / 60 AS total_overtime')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'breakDurationDistribution' => [
                'labels' => $breakDurationDistribution->pluck('duration_group')->map(function ($group) {
                    return $group . '-' . ($group + 5) . ' min';
                }),
                'values' => $breakDurationDistribution->pluck('count'),
            ],
            'overtimeTrend' => [
                'labels' => $overtimeTrend->pluck('date'),
                'values' => $overtimeTrend->pluck('total_overtime'),
            ],
        ];
    }
}
