@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">{{ __('admin.gestion_breaks') }}</h2>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">Filtrar por fecha</div>
                    <div class="card-body">
                        <form id="filtroFecha" class="form-inline">
                            <input type="date" id="fecha" name="fecha" class="form-control mr-2"
                                value="{{ $fecha }}">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">Estadísticas del Día</div>
                    <div class="card-body">
                        <p><strong>Total de breaks:</strong> <span
                                id="totalBreaks">{{ $estadisticas['total_breaks'] }}</span></p>
                        <p><strong>Duración promedio:</strong> <span
                                id="promedioDuracion">{{ round($estadisticas['promedio_duracion'], 2) }}</span> minutos</p>
                        <p><strong>Total overtime:</strong> <span
                                id="totalOvertime">{{ round($estadisticas['total_overtime'], 2) }}</span> minutos</p>
                        <p><strong>Mayor overtime:</strong> <span id="mayorOvertime">
                                @if ($estadisticas['mas_overtime'])
                                    {{ $estadisticas['mas_overtime']['usuario'] }}
                                    ({{ $estadisticas['mas_overtime']['overtime'] }} minutos)
                                @else
                                    N/A
                                @endif
                            </span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">Distribución de Duración de Breaks</div>
                    <div class="card-body">
                        <canvas id="breakDurationChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">Tendencia de Tiempo Extra</div>
                    <div class="card-body">
                        <canvas id="overtimeTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">Detalle de Breaks</div>
            <div class="card-body">
                <table id="breaksTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Duración (min)</th>
                            <th>Overtime (min)</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <style>
        .break-normal {
            background-color: #d4edda !important;
        }

        .break-warning {
            background-color: #fff3cd !important;
        }

        .break-danger {
            background-color: #f8d7da !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            let breakDurationChart, overtimeTrendChart;

            // Configuración global de DataTables
            $.extend($.fn.dataTable.defaults, {
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                }
            });

            // Inicialización de la tabla
            let table = $('#breaksTable').DataTable({
                processing: true,
                serverSide: false, // Procesamiento en el cliente
                ajax: {
                    url: "{{ route('admin.gestion-breaks.datos-tabla') }}",
                    data: function(d) {
                        d.fecha = $('#fecha').val();
                    },
                    dataSrc: '' // Asegura que se use el array devuelto directamente
                },
                columns: [
                    {data: 'usuario', name: 'usuario'},
                    {data: 'inicio', name: 'inicio'},
                    {data: 'fin', name: 'fin'},
                    {data: 'duracion', name: 'duracion'},
                    {data: 'overtime', name: 'overtime'},
                    {data: 'estado', name: 'estado'}
                ],
                createdRow: function(row, data, dataIndex) {
                    if (data.duracion <= 30) {
                        $(row).addClass('break-normal');
                    } else if (data.duracion > 30 && data.duracion <= 40) {
                        $(row).addClass('break-warning');
                    } else {
                        $(row).addClass('break-danger');
                    }
                },
                paging: false,
                searching: false,
                info: false,
                ordering: true,
                order: [[3, 'desc']], // Ordena por la columna 'duracion' en orden descendente
                dom: 't', // Solo muestra la tabla, sin otros controles
            });

            // Inicialización de los gráficos
            function initCharts(chartData) {
                const ctxDuration = document.getElementById('breakDurationChart').getContext('2d');
                breakDurationChart = new Chart(ctxDuration, {
                    type: 'bar',
                    data: {
                        labels: chartData.breakDurationDistribution.labels,
                        datasets: [{
                            label: 'Número de Breaks',
                            data: chartData.breakDurationDistribution.values,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Breaks'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Duración (minutos)'
                                }
                            }
                        }
                    }
                });

                const ctxOvertime = document.getElementById('overtimeTrendChart').getContext('2d');
                overtimeTrendChart = new Chart(ctxOvertime, {
                    type: 'line',
                    data: {
                        labels: chartData.overtimeTrend.labels,
                        datasets: [{
                            label: 'Tiempo Extra (minutos)',
                            data: chartData.overtimeTrend.values,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
                                    text: 'Tiempo Extra (minutos)'
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
            }

            // Actualización de los gráficos
            function updateCharts(chartData) {
                breakDurationChart.data.labels = chartData.breakDurationDistribution.labels;
                breakDurationChart.data.datasets[0].data = chartData.breakDurationDistribution.values;
                breakDurationChart.update();

                overtimeTrendChart.data.labels = chartData.overtimeTrend.labels;
                overtimeTrendChart.data.datasets[0].data = chartData.overtimeTrend.values;
                overtimeTrendChart.update();
            }

            // Actualización de las estadísticas
            function updateEstadisticas(data) {
                $('#totalBreaks').text(data.total_breaks);
                $('#promedioDuracion').text(data.promedio_duracion.toFixed(2));
                $('#totalOvertime').text(data.total_overtime.toFixed(2));
                if (data.mas_overtime) {
                    $('#mayorOvertime').text(`${data.mas_overtime.usuario} (${data.mas_overtime.overtime} minutos)`);
                } else {
                    $('#mayorOvertime').text('N/A');
                }
            }

            // Función para obtener los datos del dashboard
            function fetchDashboardData() {
                $.ajax({
                    url: "{{ route('admin.gestion-breaks.datos-dashboard') }}",
                    data: { fecha: $('#fecha').val() },
                    success: function(response) {
                        updateEstadisticas(response.estadisticas);
                        updateCharts(response.chartData);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching dashboard data:", error);
                    }
                });
            }

            // Manejo del evento de cambio de fecha
            $('#filtroFecha').on('submit', function(e) {
                e.preventDefault();
                table.ajax.reload();
                fetchDashboardData();
            });

            // Inicialización de los datos
            $.ajax({
                url: "{{ route('admin.gestion-breaks.datos-dashboard') }}",
                data: { fecha: $('#fecha').val() },
                success: function(response) {
                    initCharts(response.chartData);
                    updateEstadisticas(response.estadisticas);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching initial dashboard data:", error);
                }
            });
        });
        </script>
@endpush
