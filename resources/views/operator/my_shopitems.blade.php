@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h2 class="mb-4">Mi Historial de Compras</h2>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Gastado</h5>
                        <p class="card-text h3">{{ number_format($totalSpent, 0, ',', '.') }} COP</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total de Artículos Comprados</h5>
                        <p class="card-text h3">{{ $totalItems }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Gráfico de Compras por Día</h5>
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detalle de Compras</h5>
                <div class="table-responsive">
                    <table id="salesTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>
                                        @if ($sale->created_at instanceof \DateTime)
                                            {{ $sale->created_at->format('d/m/Y H:i') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i') }}
                                        @endif
                                    </td>
                                    <td>{{ $sale->product->name }}</td>
                                    <td>{{ $sale->quantity }}</td>
                                    <td>{{ number_format($sale->total_price, 0, ',', '.') }} COP</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            $('#salesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10
            });

            var ctx = document.getElementById('salesChart').getContext('2d');
            var chartData = @json($chartData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: 'Total de Compras por Día',
                        data: chartData.map(item => item.total),
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total (COP)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
