@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $viewType === 'operator' ? 'Mis Puntos' : 'Puntos del Grupo' }}</h1>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle shadow" type="button" id="viewTypeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                {{ ucfirst($viewType) }}
            </button>
            <ul class="dropdown-menu" aria-labelledby="viewTypeDropdown">
                <li><a class="dropdown-item" href="{{ route('my_points', ['view' => 'operator']) }}">Operador</a></li>
                <li><a class="dropdown-item" href="{{ route('my_points', ['view' => 'group']) }}">Grupo</a></li>
            </ul>
        </div>
    </div>

    <!-- Resumen de Puntos -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Puntos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPoints }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Puntos este Mes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyPoints }}</div>
                            <div class="text-xs {{ $monthlyPercentage >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $monthlyPercentage }}% vs mes anterior
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Puntos esta Semana</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $weeklyPoints }}</div>
                            <div class="text-xs {{ $weeklyPercentage >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $weeklyPercentage }}% vs semana anterior
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Puntos Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayPoints }}</div>
                            <div class="text-xs {{ $dailyPercentage >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $dailyPercentage }}% vs ayer
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($viewType === 'operator')
    <!-- Estadísticas adicionales solo para operador -->
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas del Operador</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h4 class="small font-weight-bold">Mejor Día <span class="float-right">{{ $bestDay }} puntos</span></h4>
                        <p class="text-muted small">Fecha: {{ \Carbon\Carbon::parse($bestDayDate)->format('d/m/Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <h4 class="small font-weight-bold">Mejor Mes <span class="float-right">{{ $bestMonth }} puntos</span></h4>
                        <p class="text-muted small">Mes: {{ \Carbon\Carbon::parse($bestMonthDate)->format('M Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <h4 class="small font-weight-bold">Promedio de Puntos <span class="float-right">{{ round($averagePoints, 2) }}</span></h4>
                    </div>
                    <div class="mb-3">
                        <h4 class="small font-weight-bold">
                            Eficiencia
                            <span class="float-right">
                                @if(isset($dailyGoal) && $dailyGoal > 0)
                                    {{ round(($todayPoints / $dailyGoal) * 100, 2) }}%
                                @else
                                    N/A
                                @endif
                            </span>
                        </h4>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: {{ isset($dailyGoal) && $dailyGoal > 0 ? min(($todayPoints / $dailyGoal) * 100, 100) : 0 }}%"
                                 aria-valuenow="{{ isset($dailyGoal) && $dailyGoal > 0 ? min(($todayPoints / $dailyGoal) * 100, 100) : 0 }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tendencia de Puntos</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="pointsTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($viewType === 'operator')
    <!-- Lista de Últimos Registros solo para operador -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Últimos Registros de Puntos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Grupo</th>
                            <th>Fecha</th>
                            <th>Puntos</th>
                            <th>Jornada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentPoints as $point)
                            <tr>
                                <td>{{ $point->group->name ?? 'N/A' }}</td>
                                <td>{{ $point->date }}</td>
                                <td>{{ $point->points }}</td>
                                <td>
                                    @if($point->shift == 'morning')
    Mañana
@elseif($point->shift == 'afternoon')
    Tarde
@elseif($point->shift == 'night')
    Noche
@else
    {{ $point->shift }}
@endif
                                    </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
@section('javascript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('pointsTrendChart').getContext('2d');
        var chartData = {!! json_encode($chartData) !!};
        var viewType = '{{ $viewType }}';

        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: viewType === 'operator' ? 'Mis Puntos' : 'Puntos del Grupo',
                    data: chartData.data.map((value, index) => ({
                        x: chartData.labels[index],
                        y: value
                    })),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            parser: 'YYYY-MM-DD',
                            unit: 'day',
                            displayFormats: {
                                day: 'DD/MM/YYYY'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Puntos'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });
    });
</script>
@endsection
