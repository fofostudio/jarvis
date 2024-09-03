@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">{{ __('admin.asistencia_registro') }}</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Top 5 Mejor Asistencia</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($statistics['top_attendance'] as $stat)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $stat['name'] }}
                                    <span class="badge bg-primary rounded-pill">{{ $stat['count'] }} dias</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Top 5 Peor Asistencia</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($statistics['top_absence'] as $stat)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $stat['name'] }}
                                    <span class="badge bg-danger rounded-pill">{{ $stat['count'] }} dias</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Top 5 Llegadas Tarde</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($statistics['top_late'] as $stat)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $stat['name'] }}
                                    <span class="badge bg-warning rounded-pill">{{ $stat['count'] }} dias</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Asistencia - Semana {{ $currentWeek->format('W') }}</h3>
                    <div>
                        <a href="{{ route('admin.session_logs.index', ['week' => $prevWeek->format('Y-W')]) }}"
                            class="btn btn-secondary">
                            {{ __('admin.previous_week') }}
                        </a>
                        <a href="{{ route('admin.session_logs.index', ['week' => $nextWeek->format('Y-W')]) }}"
                            class="btn btn-secondary">
                            {{ __('admin.next_week') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @foreach ($shifts as $shift)
                    <h4 class="mb-3">Jornada
                        @if ($shift === 'morning')
                            Mañana
                        @elseif ($shift === 'afternoon')
                            Tarde
                        @elseif ($shift === 'night')
                            Noche
                        @elseif ($shift === 'complete')
                            Completa
                        @endif
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-4">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.operator') }}</th>
                                    @foreach ($currentWeekDates as $date)
                                        <th class="text-center">
                                            {{ $date->format('D') }}<br>
                                            {{ $date->format('M j') }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendanceData[$shift] as $operatorId => $operator)
                                    <tr>
                                        <td>{{ $operator['name'] }}</td>
                                        @foreach ($currentWeekDates as $date)
                                            @php
                                                $formattedDate = $date->format('Y-m-d');
                                                $status = $operator['attendance'][$formattedDate] ?? 'absent';
                                            @endphp
                                            <td class="text-center attendance-cell" data-operator-id="{{ $operatorId }}"
                                                data-date="{{ $formattedDate }}" data-status="{{ $status }}">
                                                @if ($status === 'on_time')
                                                    A Tiempo
                                                @elseif ($status === 'late')
                                                    Llegó Tarde
                                                @elseif ($status === 'justified_absence')
                                                    Falla Justificada
                                                @elseif ($status === 'suspension')
                                                    Suspensión
                                                @elseif ($status === 'remote')
                                                    Remoto
                                                @elseif ($status === 'late_recovery')
                                                    Rec Retardo
                                                @elseif ($status === 'absence_recovery')
                                                    Rec Falla
                                                @elseif ($status === 'absent')
                                                    Ausente
                                                @else
                                                    {{ ucfirst($status) }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @if (auth()->user()->role == 'super_admin'|| auth()->user()->role == 'coordinador' )
     <!-- Modal for changing attendance status -->
     <div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel"
     aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="changeStatusModalLabel">Cambiar Estado de Asistencia</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <p>Operador: <span id="modalOperatorName"></span></p>
                 <p>Fecha: <span id="modalDate"></span></p>
                 <form id="changeStatusForm">
                     <div class="mb-3">
                         <label for="statusSelect" class="form-label">Nuevo Estado:</label>
                         <select class="form-select" id="statusSelect">
                             <option value="on_time">A Tiempo</option>
                             <option value="late">Llegó Tarde</option>
                             <option value="justified_absence">Falla Justificada</option>
                             <option value="suspension">Suspensión</option>
                             <option value="remote">Remoto</option>
                             <option value="late_recovery">Rec Retardo</option>
                             <option value="absence_recovery">Rec Falla</option>
                         </select>
                     </div>
                 </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                 <button type="button" class="btn btn-primary" id="saveStatusChange">Guardar Cambios</button>
             </div>
         </div>
     </div>
 </div>
    @endif

    <style>
        .attendance-cell {
            cursor: pointer;
        }

        .attendance-cell[data-status="on_time"] {
            background-color: #28a745;
            color: white;
        }

        .attendance-cell[data-status="late"] {
            background-color: #ffc107;
            color: black;
        }

        .attendance-cell[data-status="absent"] {
            background-color: #dc3545;
            color: white;
        }

        .attendance-cell[data-status="justified_absence"] {
            background-color: #007bff;
            color: white;
        }

        .attendance-cell[data-status="suspension"] {
            background-color: #fd7e14;
            color: white;
        }

        .attendance-cell[data-status="remote"] {
            background-color: #6f42c1;
            color: white;
        }

        .attendance-cell[data-status="late_recovery"] {
            background-color: #20c997;
            color: white;
        }

        .attendance-cell[data-status="absence_recovery"] {
            background-color: #17a2b8;
            color: white;
        }
    </style>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const attendanceCells = document.querySelectorAll('.attendance-cell');
            const modal = new bootstrap.Modal(document.getElementById('changeStatusModal'));
            const modalOperatorName = document.getElementById('modalOperatorName');
            const modalDate = document.getElementById('modalDate');
            const statusSelect = document.getElementById('statusSelect');
            const saveStatusChangeBtn = document.getElementById('saveStatusChange');

            let currentCell = null;

            attendanceCells.forEach(cell => {
                cell.addEventListener('click', function() {
                    currentCell = this;
                    const operatorId = this.dataset.operatorId;
                    const date = this.dataset.date;
                    const status = this.dataset.status;
                    const operatorName = this.closest('tr').querySelector('td').textContent;

                    modalOperatorName.textContent = operatorName;
                    modalDate.textContent = date;
                    statusSelect.value = status;

                    modal.show();
                });
            });

            saveStatusChangeBtn.addEventListener('click', function() {
                const newStatus = statusSelect.value;

                fetch('/admin/update-attendance-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            user_id: currentCell.dataset.operatorId,
                            date: currentCell.dataset.date,
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            currentCell.dataset.status = newStatus;
                            currentCell.textContent = statusSelect.options[statusSelect.selectedIndex]
                                .text;
                            modal.hide();
                        } else {
                            alert('Error al actualizar el estado de asistencia');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al actualizar el estado de asistencia');
                    });
            });
        });
    </script>
@endpush
