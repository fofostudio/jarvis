@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ __('admin.dashboard') }}</h1>

    @if(auth()->user()->role == 'super_admin' || auth()->user()->role == 'Administrador')
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

    @elseif(auth()->user()->role == 'operator')
    <!-- Operator Dashboard -->
    <!-- Assigned Group and Break Toggle -->
    <div class="row mb-4">

    </div>

    <div class="row">
        <!-- Operator's Points Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('admin.operator_points_chart') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="operatorPointsChart" data-chart-data="{{ json_encode($chartData) }}"></canvas>

                    </div>
                </div>
            </div>
        </div>


        <!-- Operator's General Data -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="h4 mb-0 text-gray-800">{{ __('admin.assigned_group') }}: {{ $assignedGroup->name ?? 'N/A' }}</h3>
                    <h5 class="mb-0">{{ __('admin.break_time') }}</h5>
                    <div class="custom-control custom-switch mt-2">

                        <input type="checkbox" class="custom-control-input" id="breakToggle" {{ $isOnBreak ? 'checked' : '' }}>
                        <label class="custom-control-label" for="breakToggle">{{ __('admin.toggle_break') }}</label>

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
                        @foreach($lastLogins as $login)
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
                        @foreach($assignedGirls as $girl)
                        <tr>
                            <td>{{ $girl->internal_id }}</td>
                            <td>{{ $girl->name }}</td>
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
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    if (document.body.classList.contains('role-admin') || document.body.classList.contains('role-super-admin')) {
        initAdminDashboard();
    } else if (document.body.classList.contains('role-operator')) {
        initOperatorDashboard();
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

function initOperatorDashboard() {
    // Operator's Points Chart
    var operatorPointsCtx = document.getElementById('operatorPointsChart');
    if (operatorPointsCtx) {
        var chartData = JSON.parse(operatorPointsCtx.dataset.chartData);
        new Chart(operatorPointsCtx.getContext('2d'), {
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

    // Break toggle functionality
    var breakToggle = document.getElementById('breakToggle');
    if (breakToggle) {
        breakToggle.addEventListener('change', function() {
            fetch('/api/toggle-break', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    is_on_break: this.checked
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Optionally show a success message
                    console.log('Break status updated successfully');
                } else {
                    // Handle error, maybe revert the toggle
                    console.error('Failed to update break status');
                    this.checked = !this.checked;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert the toggle on error
                this.checked = !this.checked;
            });
        });
    }
}
</script>
@endsection

