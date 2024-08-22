@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Mis Inicios de Sesión</h1>
    <h4>Detalles de Inicios de Sesión</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora de Inicio</th>
                <th>Hora de Salida</th>
                <th>Conteo de Logins</th>
                <th>Horas Trabajadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sessionLogs as $log)
                <tr>
                    <td>{{ $log->date->format('d-m-Y') }}</td>
                    <td>{{ $log->first_login ? $log->first_login->format('H:i:s') : '-' }}</td>
                    <td>{{ $log->last_logout ? $log->last_logout->format('H:i:s') : '-' }}</td>
                    <td>{{ $log->login_count }}</td>
                    <td>
                        @if($log->first_login && $log->last_logout)
                            {{ $log->last_logout->diffInHours($log->first_login) }} horas
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Detalles de Breaks</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Inicio</th>
                <th>Fin Esperado</th>
                <th>Fin Real</th>
                <th>Tiempo Extra</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($breakLogs as $log)
                <tr>
                    <td>{{ $log->start_time->format('d-m-Y') }}</td>
                    <td>{{ $log->start_time->format('H:i:s') }}</td>
                    <td>{{ $log->expected_end_time->format('H:i:s') }}</td>
                    <td>{{ $log->actual_end_time ? $log->actual_end_time->format('H:i:s') : '-' }}</td>
                    <td>{{ $log->overtime > 0 ? $log->overtime . ' minutos' : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row">
        <div class="col-md-6">
            <canvas id="attendanceChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="breakChart"></canvas>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <canvas id="workHoursChart"></canvas>
        </div>
        <div class="col-md-6">
            <h4>Indicadores</h4>
            <ul>
                <li>Días llegado a tiempo: {{ $indicators['onTimeCount'] }}</li>
                <li>Días llegado tarde: {{ $indicators['lateCount'] }}</li>
                <li>Días ausente: {{ $indicators['absentCount'] }}</li>
                <li>Tiempo total de breaks: {{ number_format($indicators['totalBreakTime'] / 60, 2) }} horas</li>
                <li>Promedio de horas trabajadas: {{ number_format($indicators['averageWorkHours'], 2) }} horas</li>
            </ul>
        </div>
    </div>


</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var data = @json($data);

    // Attendance Chart
    var attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Asistencia',
                data: data.attendanceData,
                backgroundColor: data.attendanceData.map(value =>
                    value === 1 ? 'rgba(75, 192, 192, 0.6)' :
                    value === 0.5 ? 'rgba(255, 206, 86, 0.6)' :
                    'rgba(255, 99, 132, 0.6)'
                ),
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1,
                    ticks: {
                        stepSize: 0.5
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Registro de Asistencia'
                }
            }
        }
    });

    // Break Time Chart
    var breakCtx = document.getElementById('breakChart').getContext('2d');
    new Chart(breakCtx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Tiempo de Break (horas)',
                data: data.breakData,
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Tiempo de Break por Día'
                }
            }
        }
    });

    // Work Hours Chart
    var workHoursCtx = document.getElementById('workHoursChart').getContext('2d');
    new Chart(workHoursCtx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Horas Trabajadas',
                data: data.workHoursData,
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Horas Trabajadas por Día'
                }
            }
        }
    });
});
</script>
@endsection
