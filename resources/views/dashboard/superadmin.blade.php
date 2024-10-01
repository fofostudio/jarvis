@extends('layouts.app')

@section('content')
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
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">{{ __('admin.dashboard') }}</h1>
        <!-- Shift Selection -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.shift_selection') }}</h6>

                        <div class="d-flex align-items-center">
                            <label class="toggle-switch mr-3">
                                <input type="checkbox" id="breakToggle">
                                <span class="slider"></span>
                            </label>
                            <input type="text" id="breakTimer" class="form-control text-center" value="00:30:00"
                                readonly>
                        </div>

                        <div class="dropdown no-arrow">
                            <form id="shiftForm" action="{{ route('dashboard') }}" method="GET">
                                <select id="shiftSelector" name="shift" class="form-select" onchange="this.form.submit()">
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift }}"
                                            {{ $selectedShift == $shift ? 'selected' : '' }}>
                                            {{ __('admin.shift_' . $shift) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Operators Cards -->
        <div class="row mb-4">
            @php
                $operators = $activeOperatorsDetails->unique('id_operador');
                $onBreakOperators = $operators->where('is_on_break', true);
                $workingOperators = $operators->where('is_on_break', false)->where('status', 'Laborando');
                $inactiveOperators = $operators->where('status', '!=', 'Laborando')->where('is_on_break', false);
            @endphp

            @foreach ($onBreakOperators as $operator)
                @include('partials.operator_card', ['operator' => $operator, 'cardClass' => 'bg-warning'])
            @endforeach

            @foreach ($workingOperators as $operator)
                @include('partials.operator_card', ['operator' => $operator, 'cardClass' => 'bg-success'])
            @endforeach
        </div>
        <!-- Inactive Operators Accordion -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header" id="inactiveOperatorsHeader">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                                data-target="#inactiveOperatorsCollapse" aria-expanded="true"
                                aria-controls="inactiveOperatorsCollapse">
                                {{ __('admin.inactive_operators') }} (<span
                                    class="inactive-count">{{ $inactiveOperators->count() }}</span>)
                            </button>
                        </h2>
                    </div>

                    <div id="inactiveOperatorsCollapse" class="collapse" aria-labelledby="inactiveOperatorsHeader">
                        <div class="card-body">
                            <div class="row">
                                @foreach ($inactiveOperators as $operator)
                                    @include('partials.operator_card', [
                                        'operator' => $operator,
                                        'cardClass' => 'bg-secondary',
                                    ])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Points Summary Cards -->
        <div class="row">
            <!-- Today's Points Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ __('admin.total_points_today') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayPoints }}</div>
                                <div class="text-xs text-gray-600">vs {{ $yesterdayPoints }} {{ __('admin.yesterday') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- This Month's Points Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    {{ __('admin.total_points_month') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $thisMonthPoints }}</div>
                                <div class="text-xs text-gray-600">vs {{ $lastMonthPoints }} {{ __('admin.last_month') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- This Week's Points Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ __('admin.total_points_week') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $thisWeekPoints }}</div>
                                <div class="text-xs text-gray-600">vs {{ $lastWeekPoints }} {{ __('admin.last_week') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Statistics Cards -->
        <div class="row">
            <!-- Total Groups Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">

                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">

                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                    {{ __('admin.active_operators') }}</div>



                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeOperators }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    {{ __('admin.total_groups') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGroups }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Girls Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ __('admin.total_girls') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGirls }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-female fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Platforms Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    {{ __('admin.total_platforms') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPlatforms }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-globe fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            // Calcular el total de puntos del mes actual
            $currentMonthTotalPoints = \App\Models\Point::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('points');

            // Calcular el total de puntos del mes anterior
            $lastMonthTotalPoints = \App\Models\Point::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('points');

            // Calcular el total acumulado de todos los puntos
            $allTimeTotalPoints = \App\Models\Point::sum('points');

            // Calcular el porcentaje de cambio
            $percentageChange =
                $lastMonthTotalPoints != 0
                    ? (($currentMonthTotalPoints - $lastMonthTotalPoints) / $lastMonthTotalPoints) * 100
                    : 100;
        @endphp
        <!-- Charts Row -->
        <div class="row">
            <!-- Daily Group Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.daily_group_points') }}</h6>
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
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.total_points_comparison') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ __('admin.current_month_total') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($currentMonthTotalPoints) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    {{ __('admin.last_month_total') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($lastMonthTotalPoints) }}
                                </div>
                                <div class="text-xs {{ $percentageChange >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $percentageChange >= 0 ? '+' : '' }}{{ number_format($percentageChange, 2) }}%
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ __('admin.all_time_total') }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($allTimeTotalPoints) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Girls per Platform Pie Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.girls_per_platform') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie">
                            <canvas id="girlsPerPlatformChart"
                                data-chart-data="{{ json_encode($girlsPerPlatform) }}"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.weekly_total_points') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="weeklyTotalChart" data-chart-data="{{ json_encode($dailyTotalData) }}"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Monthly Total Points vs Goals Chart -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.monthly_total_points_vs_goals') }}</h6>
                        <div class="dropdown no-arrow">
                            <select id="monthSelector" class="form-select">
                                <option value="current">{{ __('admin.this_month') }}</option>
                                <option value="previous">{{ __('admin.last_month') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="monthlyTotalChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection



@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutButtons = document.querySelectorAll('.close-session');

            logoutButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');

                    Swal.fire({
                        title: '¿Cerrar sesión?',
                        text: '¿Estás seguro de que quieres cerrar la sesión de este operador?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, cerrar sesión',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Mostrar loading
                            Swal.fire({
                                title: 'Cerrando sesión...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Realizar la petición AJAX para cerrar la sesión
                            fetch('/admin/close-operator-session', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    },
                                    body: JSON.stringify({
                                        user_id: userId
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: '¡Éxito!',
                                            text: 'Sesión cerrada correctamente',
                                            icon: 'success',
                                            timer: 1500
                                        }).then(() => {
                                            // Recargar la página
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Error al cerrar la sesión: ' +
                                                data.message,
                                            icon: 'error'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Error al cerrar la sesión',
                                        icon: 'error'
                                    });
                                });
                        }
                    });
                });
            });
        });
    </script>
    <script>
        function updateShift() {
            const now = new Date();
            const hour = now.getHours();
            let currentShift;

            if (hour >= 6 && hour < 14) {
                currentShift = 'morning';
            } else if (hour >= 14 && hour < 22) {
                currentShift = 'afternoon';
            } else {
                currentShift = 'night';
            }

            const shiftSelector = document.getElementById('shiftSelector');
            if (shiftSelector.value !== currentShift) {
                shiftSelector.value = currentShift;
                document.getElementById('shiftForm').submit();
            }
        }

        // Actualizar inmediatamente y luego cada minuto
        updateShift();
        setInterval(updateShift, 60000);

        // Opcional: actualizar también cuando la pestaña vuelve a estar activa
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateShift();
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            const operatorCards = {};

            initBreakToggle();
            // Inicializar el acordeón

            function initializeOperatorCards() {
                $('.operator-card').each(function() {
                    const $card = $(this);
                    const operatorId = $card.data('operator-id');
                    operatorCards[operatorId] = $card;
                });
            }

            function updateCountdown() {
                $('.countdown').each(function() {
                    const $this = $(this);
                    const startTime = moment($this.data('start'));
                    const now = moment();
                    const duration = moment.duration(startTime.add(30, 'minutes').diff(now));

                    if (duration.asSeconds() > 0) {
                        $this.text(moment.utc(duration.asMilliseconds()).format('mm:ss'));
                    } else {
                        const overtime = moment.duration(now.diff(startTime.add(30, 'minutes')));
                        $this.text('+' + moment.utc(overtime.asMilliseconds()).format('mm:ss'));
                        $this.removeClass('countdown').addClass('text-danger overtime');

                        const $card = $this.closest('.operator-card');
                        $card.removeClass('bg-warning').addClass('bg-danger');
                        $card.find('.toggle-break').prop('disabled', true);
                    }
                });
            }

            function reorderCards() {
                const $container = $('.operator-cards-container');
                const cards = Object.values(operatorCards).sort((a, b) => getCardOrder(a) - getCardOrder(b));
                $container.empty().append(cards);
            }

            function getCardOrder($card) {
                if ($card.find('.countdown').length) return 1;
                if ($card.find('.overtime').length) return 2;
                if ($card.hasClass('bg-success')) return 3;
                return 4; // Inactivo
            }

            function updateCardAfterToggle($card, response) {
                const $toggleButton = $card.find('.toggle-break');
                const $statusArea = $card.find('.operator-status');

                $toggleButton.html('<i class="fas ' + (response.is_on_break ? 'fa-stop' : 'fa-mug-hot') +
                    ' fa-xs"></i>');
                $toggleButton.attr('title', response.is_on_break ? 'Fin Break' : 'Iniciar Break');

                $card.removeClass('bg-success bg-warning bg-danger')
                    .addClass(response.is_on_break ? 'bg-warning' : 'bg-success');

                if (response.is_on_break) {
                    const now = moment();
                    $statusArea.find('.countdown, .overtime').remove();
                    $statusArea.append(
                        '<i class="fas fa-mug-hot fa-xs ml-1"></i> <span class="countdown" data-start="' + now
                        .format() + '">30:00</span>');
                } else {
                    $statusArea.find('.countdown, .overtime').remove();
                }
            }

            $(document).on('click', '.toggle-break', function() {
                const userId = $(this).data('user-id');
                const $card = operatorCards[userId];

                $.ajax({
                    url: "{{ route('toggle.break', ['userId' => ':userId']) }}".replace(':userId',
                        userId),
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCardAfterToggle($card, response);
                            reorderCards();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Ha ocurrido un error'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON && xhr.responseJSON.message ? xhr
                                .responseJSON.message :
                                'Ha ocurrido un error en la solicitud'
                        });
                    }
                });
            });

            // Inicialización
            initializeOperatorCards();
            setInterval(updateCountdown, 1000);
            setInterval(reorderCards, 5000);
            updateCountdown();
            reorderCards();

            // Prueba de SweetAlert
            // Swal.fire('Hello world!');
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initAdminDashboard();
            initGirlsPerPlatformChart();
            initBreakCountdown();
            initMonthlyTotalChart();
            var inactiveOperatorsCollapse = document.getElementById('inactiveOperatorsCollapse');
            var toggleButton = document.querySelector('#inactiveOperatorsHeader button');
            var inactiveCount = document.querySelector('.inactive-count').textContent;

            if (inactiveOperatorsCollapse && toggleButton) {
                inactiveOperatorsCollapse.addEventListener('show.bs.collapse', function() {
                    toggleButton.textContent = '{{ __('admin.hide_inactive_operators') }}';
                });

                inactiveOperatorsCollapse.addEventListener('hide.bs.collapse', function() {
                    toggleButton.textContent = '{{ __('admin.inactive_operators') }} (' + inactiveCount +
                        ')';
                });

                // Initialize the collapse
                var bsCollapse = new bootstrap.Collapse(inactiveOperatorsCollapse, {
                    toggle: false
                });

                toggleButton.addEventListener('click', function() {
                    bsCollapse.toggle();
                });
            }
        });

        function initAdminDashboard() {
            // Daily Group Chart
            var dailyGroupCtx = document.getElementById('dailyGroupChart');
            if (dailyGroupCtx) {
                var dailyGroupData = JSON.parse(dailyGroupCtx.dataset.chartData);
                new Chart(dailyGroupCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: dailyGroupData.map(item => item.group.name),
                        datasets: [{
                                label: 'Points',
                                data: dailyGroupData.map(item => item.total_points),
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            },
                            {
                                label: 'Goals',
                                data: dailyGroupData.map(item => item.total_goal),
                                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            }
                        ]
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

            // Weekly Total Chart
            var weeklyTotalCtx = document.getElementById('weeklyTotalChart');
            if (weeklyTotalCtx) {
                var weeklyTotalData = JSON.parse(weeklyTotalCtx.dataset.chartData);
                new Chart(weeklyTotalCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: weeklyTotalData.map(item => item.date),
                        datasets: [{
                                label: 'Total Points',
                                data: weeklyTotalData.map(item => item.total_points),
                                borderColor: 'rgb(75, 192, 192)',
                                tension: 0.1
                            },
                            {
                                label: 'Total Goals',
                                data: weeklyTotalData.map(item => item.total_goal),
                                borderColor: 'rgb(255, 99, 132)',
                                tension: 0.1
                            }
                        ]
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

            // Shift selector
            var shiftSelector = document.getElementById('shiftSelector');
            if (shiftSelector) {
                shiftSelector.addEventListener('change', function() {
                    window.location.href = '/dashboard?shift=' + this.value;
                });
            }
        }

        function initGirlsPerPlatformChart() {
            var ctx = document.getElementById('girlsPerPlatformChart').getContext('2d');
            var girlsPerPlatformData = JSON.parse(ctx.canvas.dataset.chartData);

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: girlsPerPlatformData.map(item => item.name),
                    datasets: [{
                        data: girlsPerPlatformData.map(item => item.girls_count),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                        ],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Girls per Platform'
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

        function initBreakCountdown() {
            const countdownElements = document.querySelectorAll('.break-countdown');

            countdownElements.forEach(element => {
                const endTime = parseInt(element.dataset.endTime);

                function updateCountdown() {
                    const now = Math.floor(Date.now() / 1000);
                    const timeLeft = endTime - now;

                    if (timeLeft > 0) {
                        const minutes = Math.floor(timeLeft / 60);
                        const seconds = timeLeft % 60;
                        element.textContent =
                            `Tiempo Restante: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    } else {
                        element.textContent = 'Break finalizado';
                        element.classList.add('break-finished');
                    }
                }

                updateCountdown();
                const intervalId = setInterval(updateCountdown, 1000);
                element.dataset.intervalId = intervalId;
            });
        }

        function clearBreakCountdowns() {
            const countdownElements = document.querySelectorAll('.break-countdown');
            countdownElements.forEach(element => {
                clearInterval(parseInt(element.dataset.intervalId));
            });
        }

        function initMonthlyTotalChart() {
            var ctx = document.getElementById('monthlyTotalChart').getContext('2d');
            var currentMonthData = @json($currentMonthData);
            var previousMonthData = @json($previousMonthData);

            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: currentMonthData.map(item => item.date),
                    datasets: [{
                            label: 'Total Points',
                            data: currentMonthData.map(item => item.total_points),
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            fill: true,
                        },
                        {
                            label: 'Total Goals',
                            data: currentMonthData.map(item => item.total_goal),
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            fill: true,
                        }
                    ]
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

            document.getElementById('monthSelector').addEventListener('change', function() {
                var selectedData = this.value === 'current' ? currentMonthData : previousMonthData;
                chart.data.labels = selectedData.map(item => item.date);
                chart.data.datasets[0].data = selectedData.map(item => item.total_points);
                chart.data.datasets[1].data = selectedData.map(item => item.total_goal);
                chart.update();
            });
        }
    </script>
@endsection
