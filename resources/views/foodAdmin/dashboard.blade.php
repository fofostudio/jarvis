@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
<style>
    .card {
        transition: all 0.3s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">Dashboard FoodAdmin</h1>

    <!-- Resumen de Ventas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Ventas de Hoy</h5>
                    <h2 class="card-text">${{ number_format($todaySales, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Ventas del Mes</h5>
                    <h2 class="card-text">${{ number_format($monthSales, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Ventas</h5>
                    <h2 class="card-text">${{ number_format($totalSales, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Promedio de Venta</h5>
                    <h2 class="card-text">${{ number_format($averageSale, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ventas por Día (Últimos 30 días)</h5>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ventas por Categoría</h5>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Productos y Balance de Operadores -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top 5 Productos</h5>
                    <ul class="list-group">
                        @foreach($topProducts as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $product->product->name }}
                                <span class="badge bg-primary rounded-pill">{{ $product->total_quantity }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Balance de Operadores</h5>
                    <ul class="list-group">
                        @foreach($operatorBalances as $balance)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $balance->user->name }}
                                <span class="badge bg-{{ $balance->balance >= 0 ? 'success' : 'danger' }} rounded-pill">
                                    ${{ number_format($balance->balance, 2) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas Recientes y Pagos Recientes -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ventas Recientes</h5>
                    <table id="recentSalesTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cliente</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSales as $sale)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $sale->product->name }}</td>
                                    <td>{{ $sale->user->name }}</td>
                                    <td>${{ number_format($sale->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pagos Recientes</h5>
                    <table id="recentPaymentsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Operador</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $payment->user->name }}</td>
                                    <td>${{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Inicializar DataTables
        $('#recentSalesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
        $('#recentPaymentsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });

        // Gráfico de Ventas por Día
        var salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($salesByDay->pluck('date')) !!},
                datasets: [{
                    label: 'Ventas por Día',
                    data: {!! json_encode($salesByDay->pluck('total')) !!},
                    borderColor: 'rgb(75, 192, 192)',
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

        // Gráfico de Ventas por Categoría
        var categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($salesByCategory->pluck('category')) !!},
                datasets: [{
                    data: {!! json_encode($salesByCategory->pluck('total')) !!},
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)'
                    ]
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
                        text: 'Ventas por Categoría'
                    }
                }
            }
        });
    });
</script>
@endsection
