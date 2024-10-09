@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Listado de Auditorías</h1>

        <!-- Estadísticas e Indicadores -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Auditorías</h5>
                        <p class="card-text display-4">{{ $totalAudits }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Promedio Calificación</h5>
                        <p class="card-text display-4">{{ number_format($averageScore, 1) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Auditorías Hoy</h5>
                        <p class="card-text display-4">{{ $auditsToday }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Chicas Auditadas</h5>
                        <p class="card-text display-4">{{ $totalGirlsAudited }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('audits.create') }}" class="btn btn-primary">Crear Nueva Auditoría</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover" id="auditsTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Fecha de Auditoría</th>
                        <th>Tipo</th>
                        <th>Auditor</th>
                        <th>Grupo/Operador</th>
                        <th>Chicas Auditadas</th>
                        <th>Calificación Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($audits as $audit)
                        <tr>
                            <td>{{ $audit->audit_date->format('d/m/Y') }}</td>
                            <td>{{ ucfirst($audit->audit_type) }}</td>
                            <td>{{ $audit->auditor->name }}</td>
                            <td>
                                @if($audit->audit_type == 'group')
                                    {{ $audit->group->name ?? 'N/A' }}
                                @else
                                    {{ $audit->operator->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td>{{ $audit->auditDetails->count() }}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ $audit->total_score }}%;"
                                        aria-valuenow="{{ $audit->total_score }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($audit->total_score, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('audits.show', $audit) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('audits.edit', $audit) }}" class="btn btn-sm btn-warning">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{ $audits->links() }}
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <style>
        .card {
            transition: transform .2s;
        }

        .card:hover {
            transform: scale(1.05);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#auditsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                pageLength: 10,
                responsive: true,
                columns: [
                    null,
                    null,
                    null,
                    null,
                    null,
                    { orderable: false },
                    { orderable: false }
                ],
                "paging": false,
                "info": false,
            });
        });
    </script>
@endpush