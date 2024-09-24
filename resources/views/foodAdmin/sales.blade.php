@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-4">Ventas</h2>

    <!-- Estadísticas generales -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h5 class="card-title">Ventas de Hoy</h5>
                    <p class="card-text h3">{{ number_format($todaySales, 0, ',', '.') }} COP</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h5 class="card-title">Ventas del Mes</h5>
                    <p class="card-text h3">{{ number_format($monthSales, 0, ',', '.') }} COP</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h5 class="card-title">Total de Ventas</h5>
                    <p class="card-text h3">{{ $totalSalesCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h5 class="card-title">Promedio por Venta</h5>
                    <p class="card-text h3">{{ number_format($averageSale, 0, ',', '.') }} COP</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Resumen por Operador -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title mb-3">Resumen por Operador</h3>
            <div class="table-responsive">
                <table class="table table-hover" id="operatorSummaryTable">
                    <thead class="table-light">
                        <tr>
                            <th>Operador</th>
                            <th>Total Ventas y Vales</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($operatorSummary as $operator)
                            <tr>
                                <td>{{ $operator['name'] }}</td>
                                <td data-order="{{ $operator['total_sales_and_vales'] }}">
                                    {{ number_format($operator['total_sales_and_vales'], 0, ',', '.') }} COP
                                </td>
                                <td data-order="{{ $operator['balance'] }}">
                                    {{ number_format($operator['balance'], 0, ',', '.') }} COP
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay datos de operadores disponibles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabla de Registro de Ventas -->
    <div class="card">
        <div class="card-body">
            <h3 class="card-title mb-3">Registro de Ventas</h3>
            <div class="table-responsive">
                <table class="table table-hover" id="salesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Total</th>
                            <th>Fecha de Venta</th>
                            <th>Vendido A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paginatedSales as $sale)
                            <tr>
                                <td>{{ $sale->product->name }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td data-order="{{ $sale->total_price }}">
                                    {{ number_format($sale->total_price, 0, ',', '.') }} COP
                                </td>
                                <td data-order="{{ $sale->created_at->timestamp }}">
                                    {{ $sale->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td>{{ $sale->user->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $paginatedSales->links() }}
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
<script>
    $(document).ready(function() {
        $('#operatorSummaryTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            "pageLength": 10,
            "ordering": true,
            "order": [[2, "desc"]], // Ordena por la columna de Balance en orden descendente
            "info": true,
            "responsive": true,
            "columnDefs": [
                {
                    "targets": [1, 2], // Aplicar a las columnas de Total Ventas y Vales, y Balance
                    "type": "num-fmt"
                }
            ]
        });

        $('#salesTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            "pageLength": 25,
            "ordering": true,
            "order": [[3, "desc"]], // Ordena por la fecha de venta en orden descendente
            "info": true,
            "responsive": true,
            "paging": false, // Desactivamos la paginación de DataTables ya que estamos usando la de Laravel
            "columnDefs": [
                {
                    "targets": [2], // Aplicar a la columna de Precio Total
                    "type": "num-fmt"
                },
                {
                    "targets": [3], // Aplicar a la columna de Fecha de Venta
                    "type": "date"
                }
            ]
        });
    });
</script>
@endpush
