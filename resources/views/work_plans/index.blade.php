@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>Planes de Trabajo</h1>

    <form action="{{ route('work_plans.index') }}" method="GET" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="group_id" class="form-label">Grupo:</label>
                <select id="group_id" name="group_id" class="form-select">
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ $selectedGroup->id == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="week" class="form-label">Semana:</label>
                <input type="number" id="week" name="week" value="{{ $week }}" class="form-control" min="1" max="53" step="1">
            </div>
            <div class="col-md-2">
                <label for="year" class="form-label">Año:</label>
                <input type="number" id="year" name="year" value="{{ $year }}" class="form-control" min="{{ date('Y') - 1 }}" max="{{ date('Y') + 1 }}" step="1">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
    </form>

    <div class="mt-3 mb-4">
        <small class="text-muted">
            Semana {{ $week }}: {{ \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek()->format('d/m/Y') }}
            - {{ \Carbon\Carbon::now()->setISODate($year, $week)->endOfWeek()->format('d/m/Y') }}
        </small>
    </div>

    @foreach(['cartas', 'mensajes', 'icebreakers'] as $type)
        <h2 class="mt-4">{{ ucfirst($type) }}</h2>
        @if(isset($workPlans[$type]))
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Turno</th>
                            <th>Lunes</th>
                            <th>Martes</th>
                            <th>Miércoles</th>
                            <th>Jueves</th>
                            <th>Viernes</th>
                            <th>Sábado</th>
                            <th>Domingo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['mañana', 'tarde', 'madrugada'] as $shift)
                            <tr>
                                <td>{{ ucfirst($shift) }}</td>
                                @for($day = 1; $day <= 7; $day++)
                                    <td>
                                        @php
                                            $assignment = $workPlans[$type]->assignments->where('day_of_week', $day)->where('shift', $shift)->first();
                                        @endphp
                                        {{ $assignment->girl->name ?? 'No asignado' }}
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>No hay plan de trabajo para {{ $type }}.</p>
        @endif
    @endforeach

    <form action="{{ route('work_plans.generate') }}" method="POST" class="mt-4">
        @csrf
        <input type="hidden" name="group_id" value="{{ $selectedGroup->id }}">
        <input type="hidden" name="week" value="{{ $week }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <button type="submit" class="btn btn-primary">Generar/Regenerar Planes de Trabajo</button>
    </form>
</div>
@endsection
