@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Mi Registro de Asistencia</h1>

        <form method="GET" action="{{ route('my_logins') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Calendario de Asistencia</h4>
                    </div>
                    <div class="card-body">
                        <div id="attendance-calendar"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Estadísticas</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @if (isset($indicators))
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Días a tiempo
                                    <span class="badge bg-success rounded-pill">{{ $indicators['on_time'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Días con llegada tarde
                                    <span class="badge bg-warning rounded-pill">{{ $indicators['late'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Días ausente
                                    <span class="badge bg-danger rounded-pill">{{ $indicators['absent'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Fallas justificadas
                                    <span
                                        class="badge bg-primary rounded-pill">{{ $indicators['justified_absence'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Suspensiones
                                    <span class="badge bg-warning rounded-pill">{{ $indicators['suspension'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Días remotos
                                    <span class="badge bg-info rounded-pill">{{ $indicators['remote'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Recuperaciones de retardo
                                    <span
                                        class="badge bg-secondary rounded-pill">{{ $indicators['late_recovery'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Recuperaciones de falla
                                    <span
                                        class="badge bg-dark rounded-pill">{{ $indicators['absence_recovery'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Tiempo total de breaks
                                    <span
                                        class="badge bg-info rounded-pill">{{ number_format($indicators['totalBreakTime'] / 60, 2) }}
                                        horas</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Promedio de horas trabajadas
                                    <span
                                        class="badge bg-primary rounded-pill">{{ number_format($indicators['averageWorkHours'], 2) }}
                                        horas</span>
                                </li>
                            @else
                                <li class="list-group-item">No hay datos de indicadores disponibles.</li>
                            @endif

                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Detalles de Inicios de Sesión</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="session-logs-table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora de Inicio</th>
                                        <th>Hora de Salida</th>
                                        <th>Horas Trabajadas</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($sessionLogs) && $sessionLogs->count() > 0)
                                        @foreach ($sessionLogs as $log)
                                            <tr>
                                                <td>{{ $log->date->format('d-m-Y') }}</td>
                                                <td>{{ $log->first_login ? $log->first_login->format('g:i A') : '-' }}</td>
                                                <td>{{ $log->last_logout ? $log->last_logout->format('g:i A') : '-' }}</td>
                                                <td>
                                                    @if ($log->first_login && $log->last_logout)
                                                        {{ $log->last_logout->diffInHours($log->first_login) }} horas
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $log->status }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5">No hay registros de sesión disponibles.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Detalles de Breaks</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="break-logs-table" class="table table-striped">
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
                                    @if (isset($breakLogs) && $breakLogs->count() > 0)
                                    @foreach ($breakLogs as $log)
                                    <tr>
                                        <td>{{ $log->start_time->format('d-m-Y') }}</td>
                                        <td>{{ $log->start_time->format('g:i A') }}</td>
                                        <td>{{ $log->expected_end_time->format('g:i A') }}</td>
                                        <td>{{ $log->actual_end_time ? $log->actual_end_time->format('g:i A') : '-' }}
                                        </td>
                                        <td>{{ $log->overtime > 0 ? $log->overtime . ' minutos' : '-' }}</td>
                                    </tr>
                                @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5">No hay registros de Break disponibles.</td>
                                    </tr>

                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css" rel="stylesheet">
    <style>
        .fc-event-dot {
            margin-right: 5px;
        }

        .fc-event-title {
            font-weight: bold;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('attendance-calendar');
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    events: @json($calendarEvents),
                    initialDate: new Date(),
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    eventContent: function(arg) {
                        return {
                            html: '<div class="fc-event-dot" style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:' +
                                arg.event.backgroundColor + '"></div>' +
                                '<div class="fc-event-title" style="display:inline-block;color:' + arg
                                .event.backgroundColor + '">' + arg.event.title + '</div>'
                        };
                    },
                    eventTimeFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short'
                    },
                    locale: 'es',
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día'
                    },
                    allDayText: 'Todo el día',
                    weekText: 'Sm',
                    moreLinkText: 'más',
                    noEventsText: 'No hay eventos para mostrar'
                });
                calendar.render();
            } else {
                console.error('Calendar element not found');
            }

            $('#session-logs-table').DataTable({
                pageLength: 5,
                lengthChange: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                searching: false,
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    type: 'date-eu',
                    targets: 0
                }],
                dom: 'tp'
            });

            $('#break-logs-table').DataTable({
                pageLength: 5,
                lengthChange: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                searching: false,
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    type: 'date-eu',
                    targets: 0
                }],
                dom: 'tp'
            });
        });
    </script>
@endpush
