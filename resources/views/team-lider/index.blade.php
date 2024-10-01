@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Checklist Diario de Tareas - Vista de Team Leader</h1>
    
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="dateForm" class="row g-3">
                <div class="col-md-4">
                    <label for="checklistDate" class="form-label">Fecha de consulta</label>
                    <input type="date" class="form-control" id="checklistDate" name="date" value="{{ $currentDate }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Consultar Checklist</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tarea</th>
                        <th>Estado</th>
                        <th>Última actualización</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="taskList">
                    @foreach($tasks as $task)
                    <tr data-task-id="{{ $task->id }}">
                        <td>{{ $task->name }}</td>
                        <td>
                            <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">{{ $task->status }}</span>
                        </td>
                        <td>{{ $task->updated_at->format('d/m/Y h:i:s A') }}</td>
                        <td>
                            @if($isCurrentDate)
                            <button class="btn btn-sm btn-outline-secondary edit-task">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($isCurrentDate)
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Agregar Nueva Tarea</h5>
        </div>
        <div class="card-body">
            <form id="newTaskForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" id="newTaskName" placeholder="Nombre de la tarea" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <select class="form-control" id="newTaskStatus">
                            <option value="En espera">En espera</option>
                            <option value="En curso">En curso</option>
                            <option value="Listo">Listo</option>
                            <option value="Detenido">Detenido</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<!-- Modal para editar tarea -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm">
                    <input type="hidden" id="editTaskId">
                    <div class="mb-3">
                        <label for="editTaskName" class="form-label">Nombre de la tarea</label>
                        <input type="text" class="form-control" id="editTaskName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTaskStatus" class="form-label">Estado</label>
                        <select class="form-control" id="editTaskStatus">
                            <option value="En espera">En espera</option>
                            <option value="En curso">En curso</option>
                            <option value="Listo">Listo</option>
                            <option value="Detenido">Detenido</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveEditTask">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const currentDate = '{{ $currentDate }}';
    let isCurrentDate = true;
    const statusColors = @json($statusColors);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getStatusColor(status) {
        return statusColors[status] || 'secondary';
    }

    function formatDate(dateString) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        return new Date(dateString).toLocaleString('es-ES', options);
    }

    function updateTaskList(tasks) {
        let taskListHtml = '';
        tasks.forEach(task => {
            taskListHtml += `
                <tr data-task-id="${task.id}">
                    <td>${task.name}</td>
                    <td><span class="badge bg-${getStatusColor(task.status)}">${task.status}</span></td>
                    <td>${formatDate(task.updated_at)}</td>
                    <td>
                        ${isCurrentDate ? '<button class="btn btn-sm btn-outline-secondary edit-task"><i class="bi bi-pencil"></i></button>' : ''}
                    </td>
                </tr>
            `;
        });
        $('#taskList').html(taskListHtml);
    }

    function toggleEditability() {
        if (isCurrentDate) {
            $('#newTaskForm').closest('.card').show();
            $('.edit-task').show();
        } else {
            $('#newTaskForm').closest('.card').hide();
            $('.edit-task').hide();
        }
    }

    $('#dateForm').on('submit', function(e) {
        e.preventDefault();
        let date = $('#checklistDate').val();
        isCurrentDate = (date === currentDate);

        $.ajax({
            url: '/api/team-lider/tasks',
            method: 'GET',
            data: { date: date },
            success: function(response) {
                updateTaskList(response.tasks);
                toggleEditability();
            },
            error: function(xhr) {
                console.error('Error al cargar el checklist:', xhr.responseText);
            }
        });
    });

    $('#newTaskForm').on('submit', function(e) {
        e.preventDefault();
        if (!isCurrentDate) {
            alert('Solo puedes agregar tareas para la fecha actual.');
            return;
        }
        let taskName = $('#newTaskName').val();
        let taskStatus = $('#newTaskStatus').val();
        
        $.ajax({
            url: '/api/team-lider/tasks',
            method: 'POST',
            data: {
                name: taskName,
                status: taskStatus,
                task_date: currentDate
            },
            success: function(response) {
                let newRow = `
                    <tr data-task-id="${response.id}">
                        <td>${response.name}</td>
                        <td><span class="badge bg-${getStatusColor(response.status)}">${response.status}</span></td>
                        <td>${formatDate(response.updated_at)}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary edit-task">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#taskList').append(newRow);
                $('#newTaskForm')[0].reset();
            },
            error: function(xhr) {
                console.error('Error al crear la tarea:', xhr.responseText);
            }
        });
    });

    $(document).on('click', '.edit-task', function() {
        if (!isCurrentDate) {
            alert('Solo puedes editar tareas de la fecha actual.');
            return;
        }
        let row = $(this).closest('tr');
        $('#editTaskId').val(row.data('task-id'));
        $('#editTaskName').val(row.find('td:eq(0)').text());
        $('#editTaskStatus').val(row.find('td:eq(1) .badge').text());
        $('#editTaskModal').modal('show');
    });

    $('#saveEditTask').on('click', function() {
        if (!isCurrentDate) {
            alert('Solo puedes editar tareas de la fecha actual.');
            return;
        }
        let taskId = $('#editTaskId').val();
        let taskName = $('#editTaskName').val();
        let taskStatus = $('#editTaskStatus').val();
        
        $.ajax({
            url: `/api/team-lider/tasks/${taskId}`,
            method: 'PUT',
            data: {
                name: taskName,
                status: taskStatus
            },
            success: function(response) {
                let row = $(`tr[data-task-id="${taskId}"]`);
                row.find('td:eq(0)').text(response.name);
                row.find('td:eq(1) .badge').text(response.status);
                row.find('td:eq(1) .badge').removeClass('bg-success bg-warning bg-danger bg-info bg-secondary')
                    .addClass(`bg-${getStatusColor(response.status)}`);
                row.find('td:eq(2)').text(formatDate(response.updated_at));
                $('#editTaskModal').modal('hide');
            },
            error: function(xhr) {
                console.error('Error al actualizar la tarea:', xhr.responseText);
            }
        });
    });
});
</script>
@endpush