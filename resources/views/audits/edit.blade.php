@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Auditoría</h1>

    <form action="{{ route('audits.update', $audit) }}" method="POST" id="auditForm">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información General</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Operador</label>
                            <input type="text" class="form-control" value="{{ $audit->operator->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Fecha de Conversación</label>
                            <input type="text" class="form-control" value="{{ $audit->conversation_date->format('d/m/Y') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Fecha de Revisión</label>
                            <input type="text" class="form-control" value="{{ $audit->review_date->format('d/m/Y') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Plataforma</label>
                            <input type="text" class="form-control" value="{{ $audit->platform->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Grupo</label>
                            <input type="text" class="form-control" value="{{ $audit->group->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Chica</label>
                            <input type="text" class="form-control" value="{{ $audit->girl->name }} ({{ $audit->girl->internal_id }})" readonly>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Nombre del Cliente</label>
                            <input type="text" class="form-control" value="{{ $audit->client_name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>ID del Cliente</label>
                            <input type="text" class="form-control" value="{{ $audit->client_id }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Estatus del Cliente</label>
                            <input type="text" class="form-control" value="{{ $audit->client_status }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Checklist de Auditoría</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        @foreach($checklistItems as $key => $item)
                            <div class="form-check mb-2">
                                <input type="checkbox" name="checklist[{{ $key }}]" id="{{ $key }}" class="form-check-input checklist-item" value="1" data-score="{{ $item['score'] }}" {{ isset($audit->checklist[$key]) && $audit->checklist[$key] ? 'checked' : '' }}>
                                <label for="{{ $key }}" class="form-check-label">{{ $item['label'] }} ({{ $item['score'] }} puntos)</label>
                            </div>
                        @endforeach
                    </div>
                </div>
    
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Evaluación y Comentarios</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="general_score">Calificación General</label>
                            <input type="number" name="general_score" id="general_score" class="form-control" min="0" max="100" step="0.1" value="{{ $audit->general_score }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="general_observation">Observación General</label>
                            <textarea name="general_observation" id="general_observation" class="form-control" rows="3" required>{{ $audit->general_observation }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="recommendations">Recomendaciones</label>
                            <textarea name="recommendations" id="recommendations" class="form-control" rows="3">{{ $audit->recommendations }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary btn-lg">Actualizar Auditoría</button>
            <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#deleteModal">
                Eliminar Auditoría
            </button>
        </div>
    </form>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta auditoría? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="{{ route('audits.destroy', $audit) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let totalScore = {{ $audit->general_score }};
    updateProgressBar(totalScore);

    $('.checklist-item').on('change', function() {
        let score = parseInt($(this).data('score'));
        if ($(this).is(':checked')) {
            totalScore += score;
        } else {
            totalScore -= score;
        }
        updateProgressBar(totalScore);
    });

    function updateProgressBar(score) {
        let percentage = Math.min(score, 100);
        $('.progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage).text(percentage + '%');
        $('#general_score').val(percentage);
    }

    $('#auditForm').on('submit', function(e) {
        e.preventDefault();
        $('#general_score').prop('readonly', false);
        this.submit();
    });
});
</script>
@endpush