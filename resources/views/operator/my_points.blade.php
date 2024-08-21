@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $viewType === 'operator' ? 'Mis Puntos' : 'Puntos del Grupo' }}</h1>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="viewTypeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                {{ ucfirst($viewType) }}
            </button>
            <ul class="dropdown-menu" aria-labelledby="viewTypeDropdown">
                <li><a class="dropdown-item" href="{{ route('my_points', ['view' => 'operator']) }}">Operador</a></li>
                <li><a class="dropdown-item" href="{{ route('my_points', ['view' => 'group']) }}">Grupo</a></li>
            </ul>
        </div>
    </div>

    <!-- Resumen de Puntos -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Puntos</h5>
                    <p class="card-text display-4">{{ $totalPoints }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Puntos este Mes</h5>
                    <p class="card-text display-4">{{ $monthlyPoints }}</p>
                    <p class="card-text {{ $monthlyPercentage >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $monthlyPercentage }}% vs mes anterior
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Puntos esta Semana</h5>
                    <p class="card-text display-4">{{ $weeklyPoints }}</p>
                    <p class="card-text {{ $weeklyPercentage >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $weeklyPercentage }}% vs semana anterior
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Puntos Hoy</h5>
                    <p class="card-text display-4">{{ $todayPoints }}</p>
                    <p class="card-text {{ $dailyPercentage >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $dailyPercentage }}% vs ayer
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($viewType === 'operator')
    <!-- Estadísticas adicionales solo para operador -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Mejor Día</h5>
                    <p class="card-text display-4">{{ $bestDay }}</p>
                    <p class="card-text">Fecha: {{ \Carbon\Carbon::parse($bestDayDate)->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Mejor Mes</h5>
                    <p class="card-text display-4">{{ $bestMonth }}</p>
                    <p class="card-text">Mes: {{ \Carbon\Carbon::parse($bestMonthDate)->format('M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Promedio de Puntos</h5>
                    <p class="card-text display-4">{{ round($averagePoints, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Gráfico de Tendencia -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Tendencia de Puntos</h5>
            <canvas id="pointsTrendChart"></canvas>
        </div>
    </div>

    @if ($viewType === 'operator')
    <!-- Lista de Últimos Registros solo para operador -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Últimos Registros de Puntos</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr> <th>Grupo</th>
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
                                <td>{{ $point->shift }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/dist/chartjs-adapter-moment.min.js"></script>
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
