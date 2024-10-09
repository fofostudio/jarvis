@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Auditoría</h1>

    <form action="{{ route('audits.update', $audit) }}" method="POST" id="auditForm">
        @csrf
        @method('PUT')

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Información General</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Auditor</label>
                            <input type="text" class="form-control" value="{{ $audit->auditor->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Tipo de Auditoría</label>
                            <input type="text" class="form-control" value="{{ ucfirst($audit->audit_type) }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Fecha de Auditoría</label>
                            <input type="date" name="audit_date" class="form-control" value="{{ $audit->audit_date->format('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>{{ $audit->audit_type == 'group' ? 'Grupo' : 'Operador' }}</label>
                            <input type="text" class="form-control" value="{{ $audit->audit_type == 'group' ? $audit->group->name : $audit->operator->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Calificación Total</label>
                            <input type="number" name="total_score" id="total_score" class="form-control" value="{{ $audit->total_score }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach($audit->auditDetails as $index => $detail)
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Detalle de Auditoría - {{ $detail->girl->name }}</h5>
            </div>
            <div class="card-body">
                <input type="hidden" name="audit_details[{{ $index }}][id]" value="{{ $detail->id }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Chica</label>
                            <input type="text" class="form-control" value="{{ $detail->girl->name }} ({{ $detail->girl->internal_id }})" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Plataforma</label>
                            <input type="text" class="form-control" value="{{ $detail->platform->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Nombre del Cliente</label>
                            <input type="text" name="audit_details[{{ $index }}][client_name]" class="form-control" value="{{ $detail->client_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label>ID del Cliente</label>
                            <input type="text" name="audit_details[{{ $index }}][client_id]" class="form-control" value="{{ $detail->client_id }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Estatus del Cliente</label>
                            <select name="audit_details[{{ $index }}][client_status]" class="form-control" required>
                                <option value="Nuevo" {{ $detail->client_status == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                                <option value="Antiguo" {{ $detail->client_status == 'Antiguo' ? 'selected' : '' }}>Antiguo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6>Checklist de Auditoría</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" style="width: {{ $detail->general_score }}%;" aria-valuenow="{{ $detail->general_score }}" aria-valuemin="0" aria-valuemax="100">{{ $detail->general_score }}%</div>
                            </div>
                            @foreach($checklistItems as $key => $item)
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="audit_details[{{ $index }}][checklist][{{ $key }}]" id="{{ $index }}_{{ $key }}" class="form-check-input checklist-item" value="1" data-score="{{ $item['score'] }}" data-index="{{ $index }}" {{ isset($detail->checklist[$key]) && $detail->checklist[$key] ? 'checked' : '' }}>
                                    <label for="{{ $index }}_{{ $key }}" class="form-check-label">{{ $item['label'] }} ({{ $item['score'] }} puntos)</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label>Calificación General</label>
                            <input type="number" name="audit_details[{{ $index }}][general_score]" class="form-control general-score" data-index="{{ $index }}" min="0" max="100" step="0.1" value="{{ $detail->general_score }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Observación General</label>
                            <textarea name="audit_details[{{ $index }}][general_observation]" class="form-control" rows="3" required>{{ $detail->general_observation }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label>Recomendaciones</label>
                            <textarea name="audit_details[{{ $index }}][recommendations]" class="form-control" rows="3">{{ $detail->recommendations }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

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
    $('.checklist-item').on('change', function() {
        let index = $(this).data('index');
        updateScore(index);
    });

    function updateScore(index) {
        let totalScore = 0;
        $(`.checklist-item[data-index="${index}"]:checked`).each(function() {
            totalScore += parseInt($(this).data('score'));
        });
        let percentage = Math.min(totalScore, 100);
        $(`input[name="audit_details[${index}][general_score]"]`).val(percentage);
        $(`input[name="audit_details[${index}][general_score]"]`).siblings('.progress-bar')
            .css('width', percentage + '%')
            .attr('aria-valuenow', percentage)
            .text(percentage + '%');
        
        updateTotalScore();
    }

    function updateTotalScore() {
        let totalScore = 0;
        let count = 0;
        $('.general-score').each(function() {
            totalScore += parseFloat($(this).val());
            count++;
        });
        let averageScore = count > 0 ? totalScore / count : 0;
        $('#total_score').val(averageScore.toFixed(2));
    }

    $('#auditForm').on('submit', function(e) {
        e.preventDefault();
        $('.general-score').prop('readonly', false);
        this.submit();
    });

    // Inicializar los scores
    $('.checklist-item').each(function() {
        updateScore($(this).data('index'));
    });
});
</script>
@endpush