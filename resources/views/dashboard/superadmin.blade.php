@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">{{ __('admin.dashboard') }}</h1>
        <!-- Shift Selection -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.shift_selection') }}</h6>
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

            @foreach ($inactiveOperators as $operator)
                @include('partials.operator_card', [
                    'operator' => $operator,
                    'cardClass' => 'bg-secondary',
                ])
            @endforeach
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
                        $this.text('Tiempo restante: ' + moment.utc(duration.asMilliseconds()).format(
                            'mm:ss'));
                    } else {
                        const overtime = moment.duration(now.diff(startTime.add(30, 'minutes')));
                        $this.text('Sobrepasó: ' + moment.utc(overtime.asMilliseconds()).format(
                            'HH:mm:ss'));
                        $this.removeClass('countdown').addClass('text-danger overtime');

                        const $card = $this.closest('.operator-card');
                        $card.removeClass('bg-warning').addClass('bg-danger text-white');
                        $card.find('.badge').removeClass('bg-dark').addClass('bg-light text-dark');
                        $card.find('.toggle-break').removeClass('btn-warning').addClass('btn-light');
                    }
                });
            }

            function reorderCards() {
                const $container = $('.operator-cards-container');
                const cards = Object.values(operatorCards).sort((a, b) => getCardOrder(a) - getCardOrder(b));
                $container.empty().append(cards);
            }

            function getCardOrder($card) {
                const status = $card.find('.status-text .badge').text().trim();
                if (status === 'Activo Break') return 1;
                if (status === 'Excede Break') return 2;
                if (status === 'Laborando') return 3;
                return 4; // Inactivo
            }

            function updateCardAfterToggle($card, response) {
                const $cardBody = $card.find('.card-body');
                const $button = $card.find('.toggle-break');
                const $statusBadge = $card.find('.status-text .badge');
                const $cardText = $card.find('.card-text');

                $button.text(response.is_on_break ? 'Finalizar Break' : 'Iniciar Break');
                $button.toggleClass('btn-dark btn-dark');

                $cardBody.removeClass('bg-success bg-warning bg-danger text-white text-dark')
                    .addClass(response.is_on_break ? 'bg-warning text-dark' : 'bg-success text-white');

                $statusBadge.text(response.is_on_break ? 'Activo Break' : 'Laborando');
                $statusBadge.removeClass('bg-light text-dark bg-dark text-white')
                    .addClass(response.is_on_break ? 'bg-dark text-white' : 'bg-dark text-white');
                if (response.is_on_break) {
                    const now = moment();
                    $cardText.find('.countdown, .overtime').remove();
                    $cardText.append('<br><span class="countdown" data-start="' + now.format() +
                        '">Tiempo restante: 30:00</span>');
                } else {
                    $card.find('.countdown, .overtime').remove();
                    $button.removeClass('btn-light');
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
