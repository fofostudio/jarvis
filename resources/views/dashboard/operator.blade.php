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
            </div>

            <!-- Operator's General Data -->
            <div class="col-xl-4 col-lg-5">
                <!-- Assigned Group and Break Toggle -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="h4 mb-0 text-gray-800">{{ $assignedGroup->name ?? 'N/A' }}</h3>
                                <p class="mb-0 text-muted">Jornada: {{ $assignedShift }}</p>
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
    <script>
        var chartData = @json($chartData);
    </script>
    <script>
        // operator-dashboard.js

        document.addEventListener('DOMContentLoaded', function() {
            initOperatorDashboard();
            initBreakToggle();
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
                        tension: 0.1
                    }, {
                        label: 'Goal',
                        data: chartData.map(item => item.goal),
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1
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

            function updateTimerDisplay(seconds) {
                const minutes = Math.floor(Math.abs(seconds) / 60);
                const remainingSeconds = Math.abs(seconds) % 60;
                breakTimer.value = `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
            }

            function updateTimerStyle(seconds) {
                breakTimer.classList.remove('active', 'warning', 'danger', 'finished');
                if (seconds > 300) breakTimer.classList.add('active');
                else if (seconds > 60) breakTimer.classList.add('warning');
                else if (seconds > -5) breakTimer.classList.add('danger');
                else breakTimer.classList.add('finished');
            }

            function startBreakTimer(startTime) {
                clearInterval(timerInterval);
                const endTime = new Date(startTime).getTime() + 30 * 60 * 1000;

                function updateTimer() {
                    const now = new Date().getTime();
                    const distance = endTime - now;
                    const seconds = Math.floor(distance / 1000);

                    updateTimerDisplay(seconds);
                    updateTimerStyle(seconds);

                    if (seconds <= -5) {
                        clearInterval(timerInterval);
                        breakToggle.checked = false;
                        breakToggle.disabled = true;
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
                        breakToggle.disabled = data.break_taken && !data.is_on_break;

                        if (data.is_on_break) {
                            startBreakTimer(data.start_time);
                        } else if (data.break_taken) {
                            updateTimerDisplay(0);
                            updateTimerStyle(-5);
                        } else {
                            updateTimerDisplay(1800);
                            updateTimerStyle(1800);
                        }
                    })
                    .catch(error => console.error('Error:', error));
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
                            throw new Error('Network response was not ok');
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
                            updateTimerDisplay(0);
                            updateTimerStyle(-5);
                            this.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.checked = !this.checked; // Revert the toggle
                        alert(`Error: ${error.message}`);
                    });
            });

            setInterval(checkBreakStatus, 60000);
            checkBreakStatus();
        }

        // Función para inicializar el DataTable de las chicas asignadas
        function initAssignedGirlsTable() {
            $('#dataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                }
            });
        }

        // Llamada a la función de inicialización del DataTable
        document.addEventListener('DOMContentLoaded', function() {
            initAssignedGirlsTable();
        });
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
