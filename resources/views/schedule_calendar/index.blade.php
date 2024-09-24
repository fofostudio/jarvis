@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Calendario de Horarios</h1>
        <div id="calendar"></div>
    </div>

    @if (auth()->user()->role != 'operator')
        <!-- Modal para editar día -->
        <div class="modal fade" id="editDayModal" tabindex="-1" role="dialog" aria-labelledby="editDayModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDayModalLabel">Editar Día</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editDayForm">
                            @csrf
                            <input type="hidden" id="editDate" name="date">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="isInverted" name="is_inverted">
                                    Jornada Invertida
                                </label>
                                <label>
                                    <input type="checkbox" id="isOptional" name="is_optional">
                                    Jornada Opcional
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="mandatoryShift">Jornada Obligatoria</label>
                                <select class="form-control" id="mandatoryShift" name="mandatory_shift">
                                    <option value="">Ninguna</option>
                                    <option value="morning">Mañana</option>
                                    <option value="afternoon">Tarde</option>
                                    <option value="night">Noche</option>
                                    <option value="complete">Completa</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="saveChanges">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/locales/es.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'es',
                initialView: 'dayGridMonth',
                initialDate: '{{ $date->toDateString() }}',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title'
                },
                events: [
                    @foreach ($calendar as $day)
                        {
                            id: '{{ $day['date'] }}',
                            title: getEventTitle({{ $day['is_inverted'] ? 'true' : 'false' }},
                                {{ $day['is_optional'] ? 'true' : 'false' }},
                                '{{ $day['mandatory_shift'] }}'),
                            start: '{{ $day['date'] }}',
                            allDay: true,
                            backgroundColor: getEventColor({{ $day['is_inverted'] ? 'true' : 'false' }},
                                {{ $day['is_optional'] ? 'true' : 'false' }},
                                '{{ $day['mandatory_shift'] }}'),
                            extendedProps: {
                                isInverted: {{ $day['is_inverted'] ? 'true' : 'false' }},
                                isOptional: {{ $day['is_optional'] ? 'true' : 'false' }},
                                mandatoryShift: '{{ $day['mandatory_shift'] }}'
                            }
                        },
                    @endforeach
                ],
                dateClick: function(info) {
                    openEditModal(info.dateStr);
                },
                eventClick: function(info) {
                    openEditModal(info.event.id);
                }
            });
            calendar.render();

            function getEventTitle(isInverted, isOptional, mandatoryShift) {
                let title = isInverted ? 'Invertido' : 'Normal';
                if (isOptional) {
                    title += ' (Opcional)';
                }
                if (mandatoryShift) {
                    title += ' - Obligatorio: ' + capitalizeFirstLetter(mandatoryShift);
                }
                return title;
            }

            function getEventColor(isInverted, isOptional, mandatoryShift) {
                if (mandatoryShift) {
                    return '#b88400'; // Gold for mandatory days
                }
                if (isOptional) {
                    return '#8d8d8d'; // Gray for optional days
                }
                return isInverted ? '#a32a0f' : '#1b7502';
            }

            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            function openEditModal(date) {
                var event = calendar.getEventById(date);
                $('#editDate').val(date);
                $('#isInverted').prop('checked', event ? event.extendedProps.isInverted : false);
                $('#isOptional').prop('checked', event ? event.extendedProps.isOptional : false);
                $('#mandatoryShift').val(event ? event.extendedProps.mandatoryShift : '');
                $('#editDayModal').modal('show');
            }

            $('#saveChanges').click(function() {
                var formData = {
                    date: $('#editDate').val(),
                    is_inverted: $('#isInverted').is(':checked'),
                    is_optional: $('#isOptional').is(':checked'),
                    mandatory_shift: $('#mandatoryShift').val() || null
                };

                $.ajax({
                    url: '{{ route('schedule-calendar.update-day') }}',
                    method: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        var event = calendar.getEventById(response.date);
                        if (event) {
                            event.remove();
                        }
                        calendar.addEvent({
                            id: response.date,
                            title: getEventTitle(response.is_inverted, response
                                .is_optional, response.mandatory_shift),
                            start: response.date,
                            allDay: true,
                            backgroundColor: getEventColor(response.is_inverted,
                                response.is_optional, response.mandatory_shift),
                            extendedProps: {
                                isInverted: response.is_inverted,
                                isOptional: response.is_optional,
                                mandatoryShift: response.mandatory_shift
                            }
                        });
                        $('#editDayModal').modal('hide');
                    },
                    error: function(xhr) {
                        console.error('Error response:', xhr.responseText);
                        var errorMessage = 'Error al guardar los cambios';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage += ':\n';
                            for (var field in xhr.responseJSON.errors) {
                                errorMessage += field + ': ' + xhr.responseJSON.errors[field]
                                    .join(', ') + '\n';
                            }
                        }
                        alert(errorMessage);
                    }
                });
            });
        });
    </script>
@endsection
