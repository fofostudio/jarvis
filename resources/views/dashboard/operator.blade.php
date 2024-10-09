@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">{{ __('admin.dashboard') }}</h1>

        <!-- Operator Dashboard -->
        <div class="row">
            <!-- Operator's Points Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.operator_points_chart') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="operatorPointsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.ultimo_corte_en_sistema') }}</h6>
                        <div class="text-xs font-weight-bold text-primary">
                            Último registro: {{ \Carbon\Carbon::parse($latestPointsDate)->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="dailyGroupChart" data-chart-data="{{ json_encode($dailyGroupData) }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Operator's General Data -->
            <div class="col-xl-4 col-lg-5">
                <!-- Assigned Group and Break Toggle -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="h4 mb-0 text-gray-800">{{ $assignedGroup->name ?? 'N/A' }}</h3>
                                <p class="mb-0 text-muted">Jornada: @if ($assignedShift == 'morning')
                                        Mañana
                                    @elseif($assignedShift == 'afternoon')
                                        Tarde
                                    @elseif($assignedShift == 'night')
                                        Noche
                                    @else
                                        {{ $assignedShift }}
                                    @endif
                                </p>
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="toggle-switch mr-3">
                                    <input type="checkbox" id="breakToggle">
                                    <span class="slider"></span>
                                </label>
                                <input type="text" id="breakTimer" class="form-control text-center" value="00:30:00"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.operator_general_data') }}</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('admin.total_points') }}:</strong> {{ $totalPoints }}</p>
                        <p><strong>{{ __('admin.total_goal') }}:</strong> {{ $totalGoal }}</p>
                        <p><strong>{{ __('admin.assigned_girls') }}:</strong> {{ $assignedGirls->count() }}</p>
                    </div>
                </div>
                <!-- Estado del Plan de Trabajo de Hoy -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Plan de Trabajo de Hoy</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach (['mensajes', 'icebreakers', 'cartas'] as $type)
                                <div class="col-md-4 mb-3">
                                    <div class="card {{ $completedPlans[$type] ? 'bg-success text-white' : 'bg-light' }}">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">{{ ucfirst($type) }}</h5>
                                            @if ($completedPlans[$type])
                                                <i class="fas fa-check-circle fa-2x"></i>
                                                <p class="mb-0">Completado</p>
                                            @else
                                                <i class="fas fa-times-circle fa-2x"></i>
                                                <p class="mb-0">Pendiente</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Últimos Reportes Operativos del Grupo -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Últimos Reportes Operativos del Grupo</h6>
                    </div>
                    <div class="card-body">
                        @if ($latestGroupReports->isNotEmpty())
                            @foreach ($latestGroupReports as $report)
                                <div class="mb-3">
                                    <h6>{{ $report->report_date->format('d/m/Y') }} - {{ $report->user->name }}</h6>
                                    <p>
                                        <strong>Tipo:</strong>
                                        @if ($report->report_type === 'manual')
                                            Sugerencia/Queja/Reclamo
                                        @elseif($report->report_type === 'conversational')
                                            Conversacional
                                        @endif
                                    </p>
                                    <p><strong>Estado:</strong> {{ $report->status }}</p>
                                    @if ($report->is_approved === false)
                                        <p class="text-danger">{{ $report->auditor_comment }}</p>
                                    @endif
                                    <a href="{{ route('operative-reports.show', $report->id) }}"
                                        class="btn btn-sm btn-primary">Ver Detalles</a>
                                </div>
                                @if (!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        @else
                            <p>No hay reportes recientes.</p>
                        @endif
                    </div>
                </div>
                <!-- Last Logins -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.last_logins') }}</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($lastLogins as $login)
                                <li class="list-group-item">
                                    {{ $login->created_at->format('d/m/Y H:i:s') }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Girls Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.assigned_girls') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>{{ __('admin.id_interno') }}</th>
                                <th>{{ __('admin.name') }}</th>
                                <th>{{ __('admin.platform') }}</th>
                                <th>{{ __('admin.username') }}</th>
                                <th>{{ __('admin.password') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignedGirls as $girl)
                                <tr>
                                    <td>{{ $girl->internal_id }}</td>
                                    <td><strong>{{ $girl->name }}</strong></td>
                                    <td>{{ $girl->platform->name }}</td>
                                    <td>{{ $girl->username }}</td>
                                    <td>{{ $girl->password }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var chartData = @json($chartData);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initOperatorDashboard();
            initDailyGroupChart();
            initBreakToggle();
            initAssignedGirlsTable();
        });

        function initOperatorDashboard() {
            var ctx = document.getElementById('operatorPointsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: 'Points',
                        data: chartData.map(item => item.points),
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: true
                    }, {
                        label: 'Goal',
                        data: chartData.map(item => item.goal),
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function initDailyGroupChart() {
            var ctx = document.getElementById('dailyGroupChart').getContext('2d');
            var dailyGroupData = JSON.parse(document.getElementById('dailyGroupChart').dataset.chartData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dailyGroupData.map(item => item.group.name),
                    datasets: [{
                        label: 'Puntos',
                        data: dailyGroupData.map(item => item.total_points),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Meta',
                        data: dailyGroupData.map(item => item.total_goal),
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function initBreakToggle() {
            const breakToggle = document.getElementById('breakToggle');
            const breakTimer = document.getElementById('breakTimer');
            let timerInterval;
            let breakStartTime;
            let isBreakFinished = false;

            function updateTimerDisplay(seconds) {
                const isNegative = seconds < 0;
                seconds = Math.abs(seconds);
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const remainingSeconds = seconds % 60;
                breakTimer.value =
                    `${isNegative ? '-' : ''}${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
            }

            function updateTimerStyle(seconds) {
                breakTimer.classList.remove('active', 'warning', 'danger', 'finished');
                if (seconds > 300) breakTimer.classList.add('active');
                else if (seconds > 60) breakTimer.classList.add('warning');
                else if (seconds > 0) breakTimer.classList.add('danger');
                else breakTimer.classList.add('finished');
            }

            function startBreakTimer(startTime) {
                clearInterval(timerInterval);
                breakStartTime = new Date(startTime).getTime();
                const endTime = breakStartTime + 30 * 60 * 1000;

                function updateTimer() {
                    const now = new Date().getTime();
                    const distance = endTime - now;
                    const seconds = Math.floor(distance / 1000);

                    if (seconds <= 0 && !isBreakFinished) {
                        isBreakFinished = true;
                    }

                    updateTimerDisplay(seconds);
                    updateTimerStyle(seconds);

                    if (isBreakFinished) {
                        breakToggle.disabled = false;
                    }
                }

                updateTimer();
                timerInterval = setInterval(updateTimer, 1000);
            }

            function checkBreakStatus() {
                fetch('/break-status')
                    .then(response => response.json())
                    .then(data => {
                        breakToggle.checked = data.is_on_break;
                        breakToggle.disabled = false;

                        if (data.is_on_break) {
                            startBreakTimer(data.start_time);
                        } else {
                            updateTimerDisplay(1800);
                            updateTimerStyle(1800);
                            isBreakFinished = false;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function logOvertime(overtime) {
                fetch('/log-overtime', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            overtime: overtime
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to log overtime');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Overtime logged successfully:', data);
                    })
                    .catch(error => {
                        console.error('Error logging overtime:', error);
                    });
            }

            breakToggle.addEventListener('change', function() {
                const action = this.checked ? 'start-break' : 'end-break';
                fetch(`/${action}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Ya has tomado tu Break Hoy');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        if (action === 'start-break') {
                            startBreakTimer(data.start_time);
                        } else {
                            clearInterval(timerInterval);
                            const breakEndTime = new Date().getTime();
                            const breakDuration = Math.floor((breakEndTime - breakStartTime) / 1000);
                            const overtime = Math.max(0, breakDuration - 1800);
                            updateTimerDisplay(breakDuration - 1800);
                            updateTimerStyle(0);
                            isBreakFinished = false;

                            if (overtime > 0) {
                                logOvertime(overtime);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.checked = !this.checked; // Revert the toggle
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: `Error: ${error.message}`,
                        });
                    });
            });

            setInterval(checkBreakStatus, 60000);
            checkBreakStatus();
        }

        function initAssignedGirlsTable() {
            $('#dataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                }
            });
        }
    </script>


    <style>
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        #breakTimer {
            width: 100px;
            text-align: center;
        }

        #breakTimer.active {
            background-color: #90EE90;
        }

        #breakTimer.warning {
            background-color: #FFFFE0;
        }

        #breakTimer.danger {
            background-color: #FFB6C1;
        }

        #breakTimer.finished {
            background-color: #D3D3D3;
        }
    </style>
@endsection
