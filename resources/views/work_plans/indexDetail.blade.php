@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Gestión de Planes de Trabajo</h1>
        <p class="mb-4">Visualice el estado de los planes de trabajo y las estadísticas generales.</p>
        <style>
            .card-title {
                font-size: 0.9rem;
            }

            .card-body {
                padding: 0.5rem !important;
            }

            .badge-pill {
                padding: 0.25em 0.6em;
                font-size: 0.75rem;
                border-radius: 10rem;
            }

            .badge-success {
                background-color: #158845;
            }

            .badge-secondary {
                background-color: #6c757d;
            }

            .badge-success,
            .badge-secondary {
                color: white;
            }

            .d-flex.justify-content-between {
                gap: 0.3rem;
            }
        </style>
        <style>
            .btn-circle {
                width: 30px;
                height: 30px;
                padding: 6px 0px;
                border-radius: 15px;
                text-align: center;
                font-size: 12px;
                line-height: 1.42857;
            }
        </style>

        <!-- Indicadores por grupo -->
        <div class="row">
            @foreach ($groupIndicators as $groupId => $indicator)
                @php
                    $allLoaded = $indicator['mensajes'] && $indicator['icebreakers'] && $indicator['cartas'];
                    $cardClass = $allLoaded ? 'bg-dark text-white' : 'bg-light';
                @endphp
                <div class="col-md-2 col-sm-2 mb-3">
                    <div class="card shadow-sm h-100 {{ $cardClass }}">
                        <div class="card-body p-2">
                            <h6 class="card-title mb-2 font-weight-bold">{{ $indicator['name'] }}</h6>
                            <div class="d-flex justify-content-between">
                                <div class="d-flex justify-content-between">
                                    <span
                                        class="badge badge-pill {{ $indicator['mensajes'] ? 'badge-success' : 'badge-secondary' }}">
                                        Mensajes
                                    </span>
                                    <span
                                        class="badge badge-pill {{ $indicator['icebreakers'] ? 'badge-success' : 'badge-secondary' }}">
                                        Icebreakers
                                    </span>
                                    <span
                                        class="badge badge-pill {{ $indicator['cartas'] ? 'badge-success' : 'badge-secondary' }}">
                                        Cartas
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Estadísticas -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Planes de Trabajo
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Planes de Mensajes
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['mensajes'] }}</div>
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
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Planes de Icebreakers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['icebreakers'] }}</div>
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
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Planes de Cartas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['cartas'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       <!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="collapseFilters">
        <div class="card-body">
            <form action="{{ route('work-detail.indexDetail') }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-clock mr-2"></i>Turno</h6>
                                <select class="form-control custom-select" id="shift" name="shift">
                                    <option value="">Todos los turnos</option>
                                    @foreach($shifts as $shiftOption)
                                        <option value="{{ $shiftOption }}" {{ request('shift') == $shiftOption ? 'selected' : '' }}>
                                            {{ ucfirst($shiftOption) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Fecha</h6>
                                <input type="date" class="form-control" id="date" name="date" value="{{ request('date', now()->toDateString()) }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-user mr-2"></i>Usuario</h6>
                                <select class="form-control custom-select" id="user_id" name="user_id">
                                    <option value="">Todos los usuarios</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-users mr-2"></i>Grupo</h6>
                                <select class="form-control custom-select" id="group_id" name="group_id">
                                    <option value="">Todos los grupos</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-calendar-check mr-2"></i>Tipo de Calendario</h6>
                                <select class="form-control custom-select" id="calendar_type" name="calendar_type">
                                    <option value="">Todos los tipos</option>
                                    <option value="mensajes" {{ request('calendar_type') == 'mensajes' ? 'selected' : '' }}>Mensajes</option>
                                    <option value="icebreakers" {{ request('calendar_type') == 'icebreakers' ? 'selected' : '' }}>Icebreakers</option>
                                    <option value="cartas" {{ request('calendar_type') == 'cartas' ? 'selected' : '' }}>Cartas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('work_plans.index') }}" class="btn btn-success btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-plus-circle"></i>
                                </span>
                                <span class="text">Crear & Gestionar Planes de trabajo</span>
                            </a>
                            <button type="submit" class="btn btn-primary btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-filter"></i>
                                </span>
                                <span class="text">Aplicar Filtros</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .card-body {
        padding: 1rem;
    }
   
    .custom-select, .form-control {
        font-size: 0.9rem;
    }
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
        <!-- Tabla de Planes de Trabajo -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Planes de Trabajo</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Grupo</th>
                                <th>Tipo de Calendario</th>
                                <th>Cantidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workPlans as $workPlan)
                                <tr>
                                    <td>{{ $workPlan->created_at->format('d/m/Y h:i A') }}</td>
                                    <td>{{ $workPlan->user->name }}</td>
                                    <td>{{ $workPlan->user->groups->first()->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($workPlan->calendar_type) }}</td>
                                    <td>{{ $workPlan->cantidad }}</td>
                                    <td>
                                        <a href="{{ route('work-detail.showDetail', $workPlan) }}"
                                            class="btn btn-dark btn-circle btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
             
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "order": [
                    [0, "desc"]
                ],
                "paging": false
            });
        });
    </script>
@endpush
