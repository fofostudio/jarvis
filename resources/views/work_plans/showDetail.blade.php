@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Detalles del Plan de Trabajo</h1>
        <p class="mb-4">Visualización detallada del plan de trabajo seleccionado.</p>

        <div class="row">
            <!-- Información general -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Información General
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $workPlanDetail->user->name }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ $workPlanDetail->user->groups->first()->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600">{{ $workPlanDetail->date->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-600">{{ ucfirst($workPlanDetail->calendar_type) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la chica y jornada -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Asignación</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $workPlanAssignment->girl->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600">Jornada: {{ ucfirst($workPlanAssignment->shift) }}</div>
                                <div class="text-sm text-gray-600">Día: {{ $workPlanAssignment->day_of_week }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cantidad -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Cantidad</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $workPlanDetail->cantidad }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensaje -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Mensaje</h6>
                    </div>
                    <div class="card-body">
                        {{ $workPlanDetail->mensaje }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Screenshots -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Capturas de pantalla</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $screenshots = json_decode($workPlanDetail->screenshot_paths, true);
                            @endphp
                            @if (is_array($screenshots) && count($screenshots) > 0)
                                @foreach ($screenshots as $screenshot)
                                    <div class="col-md-4 col-sm-6 mb-4">
                                        <a href="{{ asset('storage/' . $screenshot) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $screenshot) }}" class="img-fluid rounded"
                                                alt="Captura de pantalla">
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">No hay capturas de pantalla disponibles.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4">
                <a href="{{ route('work-detail.indexDetail') }}" class="btn btn-secondary">Volver</a>
                <a href="{{ route('work-detail.editDetail', $workPlanDetail) }}" class="btn btn-primary">Editar</a>
                <form action="{{ route('work-detail.destroyDetail', $workPlanDetail) }}" method="POST"
                    style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('¿Está seguro de que desea eliminar este plan de trabajo?')">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-body {
            padding: 1.25rem;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .text-sm {
            font-size: 0.85rem;
        }

        .img-fluid {
            max-height: 200px;
            object-fit: cover;
        }
    </style>
@endpush
