@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ __('admin.dashboard') }}</h1>
    @php
$currentBreaks = \App\Models\BreakLog::with('user')
    ->whereDate('start_time', \Carbon\Carbon::today())
    ->whereNull('actual_end_time')
    ->get();

$overtimeBreaks = \App\Models\BreakLog::with('user')
    ->whereDate('start_time', \Carbon\Carbon::today())
    ->where('overtime', '>', 0)
    ->get();
@endphp

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Current and Overtime Breaks</h6>
            </div>
            <div class="card-body">
                <div id="breakCarousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($currentBreaks->merge($overtimeBreaks)->chunk(4) as $breakChunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row">
                                    @foreach ($breakChunk as $break)
                                        <div class="col-md-3 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $break->user->name }}</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted">
                                                        {{ $break->overtime ? 'Overtime Break' : 'Current Break' }}
                                                    </h6>
                                                    <p class="card-text">
                                                        Start Time: {{ $break->start_time->format('H:i:s') }}<br>
                                                        @if ($break->overtime)
                                                            Overtime: {{ gmdate('H:i:s', $break->overtime) }}
                                                        @else
                                                            Remaining Time: {{ $break->expected_end_time->diffForHumans(null, true) }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#breakCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#breakCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
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
                        <div class="text-xs text-gray-600">vs {{ $yesterdayPoints }} {{ __('admin.yesterday') }}</div>
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
                        <div class="text-xs text-gray-600">vs {{ $lastMonthPoints }} {{ __('admin.last_month') }}</div>
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
                        <div class="text-xs text-gray-600">vs {{ $lastWeekPoints }} {{ __('admin.last_week') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.shift_selection') }}</h6>
                    <div class="dropdown no-arrow">
                        <select id="shiftSelector" class="form-select" onchange="this.form.submit()">
                            @foreach($shifts as $shift)
                                <option value="{{ $shift }}" {{ $selectedShift == $shift ? 'selected' : '' }}>
                                    {{ __('admin.shift_' . $shift) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Super Admin / Admin Dashboard -->
    <div class="row">
        <!-- Active Operators Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('admin.active_operators') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeOperators }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Groups Card -->
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
    <div class="row">
        <!-- Daily Group Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.daily_group_points') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="dailyGroupChart" data-chart-data="{{ json_encode($dailyGroupData) }}"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Total Points Chart -->
        <div class="col-xl-6 col-lg-6">
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
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initAdminDashboard();
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
                    datasets: [
                        {
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
                    datasets: [
                        {
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
</script>
@endsection
