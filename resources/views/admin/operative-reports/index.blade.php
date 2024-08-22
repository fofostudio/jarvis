@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Gestión de Reportes Operativos</h1>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Indicadores en tarjetas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Reportes Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Reportes Rechazados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rejectedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Reportes Aprobados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Reportes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Reportes Pendientes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Reportes Pendientes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Operador</th>
                            <th>Grupo</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingReports as $report)
                            <tr>
                                <td>{{ $report->report_date->format('d/m/Y') }}</td>
                                <td>{{ $report->user->name }}</td>
                                <td>{{ $report->group->name }}</td>
                                <td>
                                    @if($report->report_type === 'conversational')
                                        Conversacional
                                    @elseif($report->report_type === 'manual')
                                        Quejas/Reclamos
                                    @else
                                        {{ $report->report_type }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('operative-reports.show', $report) }}" class="btn btn-info btn-sm">Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabla de Reportes Aprobados y Rechazados -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Reportes Aprobados y Rechazados</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Operador</th>
                            <th>Grupo</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reviewedReports as $report)
                            <tr>
                                <td>{{ $report->report_date->format('d/m/Y') }}</td>
                                <td>{{ $report->user->name }}</td>
                                <td>{{ $report->group->name }}</td>
                                <td>
                                    @if($report->report_type === 'conversational')
                                        Conversacional
                                    @elseif($report->report_type === 'manual')
                                        Quejas/Reclamos
                                    @else
                                        {{ $report->report_type }}
                                    @endif
                                </td>
                                <td>
                                    @if ($report->is_approved)
                                        <span class="badge badge-success bg-success">Aprobado</span>
                                    @else
                                        <span class="badge badge-danger bg-danger">Rechazado</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('operative-reports.show', $report) }}" class="btn btn-dark btn-sm">Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $reviewedReports->links() }}
        </div>
    </div>
</div>
@endsection
