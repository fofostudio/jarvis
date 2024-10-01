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
                                    <input type="checkbox" id="isRotative" name="is_inverted">
                                    Jornada Rotativa
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
                                    <option value="night">Madrugada</option>
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
                events: function(fetchInfo, successCallback, failureCallback) {
                    var events = [];
                    @foreach ($calendar as $day)
                        var isRotative = {{ $day['is_inverted'] ? 'true' : 'false' }};
                        var isOptional = {{ $day['is_optional'] ? 'true' : 'false' }};
                        var mandatoryShift = '{{ $day['mandatory_shift'] }}';
                        var date = '{{ $day['date'] }}';

                        var shifts = ['morning', 'afternoon', 'night'];
                        var colors = {
                            morning: '#4A90E2', // Azul claro
                            afternoon: '#7ED321', // Verde claro
                            night: '#F5A623' // Naranja claro
                        };

                        shifts.forEach((shift) => {
                            var displayedShift = shift;
                            if (isRotative && (shift === 'morning' || shift === 'afternoon')) {
                                displayedShift = (shift === 'morning') ? 'afternoon' :
                                'morning';
                            }

                            var color = colors[displayedShift];
                            if (isOptional) {
                                color = '#a8a7a7'; // Gris claro
                            } else if (mandatoryShift === shift) {
                                color = pSBC(-0.3, color); // Ligeramente más oscuro
                            }

                            events.push({
                                id: date + '-' + shift,
                                title: getShiftTitle(displayedShift, isRotative,
                                    isOptional, mandatoryShift === shift),
                                start: date,
                                allDay: true,
                                backgroundColor: color,
                                extendedProps: {
                                    isRotative: isRotative,
                                    isOptional: isOptional,
                                    mandatoryShift: mandatoryShift,
                                    shift: shift,
                                    displayedShift: displayedShift
                                }
                            });
                        });
                    @endforeach
                    events.sort((a, b) => {
                        if (a.start !== b.start) {
                            return new Date(a.start) - new Date(b.start);
                        }
                        return getShiftOrder(a.extendedProps.shift) - getShiftOrder(b
                            .extendedProps.shift);
                    });
                    successCallback(events);
                },
                eventClick: function(info) {
                    openEditModal(info.event.start);
                },
                eventOrder: function(a, b) {
                    return getShiftOrder(a.extendedProps.shift) - getShiftOrder(b.extendedProps.shift);
                }
            });
            calendar.render();

            function getShiftTitle(shift, isRotative, isOptional, isMandatory) {
                let title = translateShift(shift);
                if (isRotative) {
                    title += ' (Rotativa)';
                }
                if (isOptional) {
                    title += ' (Opcional)';
                }
                if (isMandatory) {
                    title += ' (Obligatoria)';
                }
                return title;
            }

            function translateShift(shift) {
                const translations = {
                    'morning': 'Mañana',
                    'afternoon': 'Tarde',
                    'night': 'Noche'
                };
                return translations[shift] || shift;
            }

            function getShiftOrder(shift) {
                const order = {
                    'morning': 0,
                    'afternoon': 1,
                    'night': 2
                };
                return order[shift] !== undefined ? order[shift] : 3;
            }

            function pSBC(p, c0, c1, l) {
                let r, g, b, P, f, t, h, i = parseInt,
                    m = Math.round,
                    a = typeof(c1) == "string";
                if (typeof(p) != "number" || p < -1 || p > 1 || typeof(c0) != "string" || (c0[0] != 'r' && c0[0] !=
                        '#') || (c1 && !a)) return null;
                if (!this.pSBCr) this.pSBCr = (d) => {
                    let n = d.length,
                        x = {};
                    if (n > 9) {
                        [r, g, b, a] = d = d.split(","), n = d.length;
                        if (n < 3 || n > 4) return null;
                        x.r = i(r[3] == "a" ? r.slice(5) : r.slice(4)), x.g = i(g), x.b = i(b), x.a = a ?
                            parseFloat(a) : -1
                    } else {
                        if (n == 8 || n == 6 || n < 4) return null;
                        if (n < 6) d = "#" + d[1] + d[1] + d[2] + d[2] + d[3] + d[3] + (n > 4 ? d[4] + d[4] :
                            "");
                        d = i(d.slice(1), 16);
                        if (n == 9 || n == 5) x.r = d >> 24 & 255, x.g = d >> 16 & 255, x.b = d >> 8 & 255, x
                            .a = m((d & 255) / 0.255) / 1000;
                        else x.r = d >> 16, x.g = d >> 8 & 255, x.b = d & 255, x.a = -1
                    }
                    return x
                };
                h = c0.length > 9, h = a ? c1.length > 9 ? true : c1 == "c" ? !h : false : h, f = this.pSBCr(c0),
                    P = p < 0, t = c1 && c1 != "c" ? this.pSBCr(c1) : P ? {
                        r: 0,
                        g: 0,
                        b: 0,
                        a: -1
                    } : {
                        r: 255,
                        g: 255,
                        b: 255,
                        a: -1
                    }, p = P ? p * -1 : p, P = 1 - p;
                if (!f || !t) return null;
                if (l) r = m(P * f.r + p * t.r), g = m(P * f.g + p * t.g), b = m(P * f.b + p * t.b);
                else r = m((P * f.r ** 2 + p * t.r ** 2) ** 0.5), g = m((P * f.g ** 2 + p * t.g ** 2) ** 0.5), b =
                    m((P * f.b ** 2 + p * t.b ** 2) ** 0.5);
                a = f.a, t = t.a, f = a >= 0 || t >= 0, a = f ? a < 0 ? t : t < 0 ? a : a * P + t * p : 0;
                if (h) return "rgb" + (f ? "a(" : "(") + r + "," + g + "," + b + (f ? "," + m(a * 1000) / 1000 :
                    "") + ")";
                else return "#" + (4294967296 + r * 16777216 + g * 65536 + b * 256 + (f ? m(a * 255) : 0)).toString(
                    16).slice(1, f ? undefined : -2)
            }

            function openEditModal(date) {
                $('#editDate').val(date.toISOString().split('T')[0]);
                var events = calendar.getEvents().filter(e => e.start.toISOString().split('T')[0] === date
                    .toISOString().split('T')[0]);
                if (events.length > 0) {
                    $('#isRotative').prop('checked', events[0].extendedProps.isRotative);
                    $('#isOptional').prop('checked', events[0].extendedProps.isOptional);
                    $('#mandatoryShift').val(events[0].extendedProps.mandatoryShift);
                } else {
                    $('#isRotative').prop('checked', false);
                    $('#isOptional').prop('checked', false);
                    $('#mandatoryShift').val('');
                }
                $('#editDayModal').modal('show');
            }

            $('#saveChanges').click(function() {
                var formData = {
                    date: $('#editDate').val(),
                    is_inverted: $('#isRotative').is(':checked'),
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
                        calendar.getEvents().forEach(event => {
                            if (event.start.toISOString().split('T')[0] === response
                                .date) {
                                event.remove();
                            }
                        });

                        var shifts = ['morning', 'afternoon', 'night'];
                        var colors = {
                            morning: '#4A90E2', // Azul claro
                            afternoon: '#7ED321', // Verde claro
                            night: '#F5A623' // Naranja claro
                        };

                        shifts.forEach((shift) => {
                            var displayedShift = shift;
                            if (response.is_inverted && (shift === 'morning' ||
                                    shift === 'afternoon')) {
                                displayedShift = (shift === 'morning') ? 'afternoon' :
                                    'morning';
                            }

                            var color = colors[displayedShift];
                            if (response.is_optional) {
                                color = '#a8a7a7'; // Gris claro
                            } else if (response.mandatory_shift === shift) {
                                color = pSBC(-0.3, color); // Ligeramente más oscuro
                            }

                            calendar.addEvent({
                                id: response.date + '-' + shift,
                                title: getShiftTitle(displayedShift, response
                                    .is_inverted, response.is_optional,
                                    response.mandatory_shift === shift),
                                start: response.date,
                                allDay: true,
                                backgroundColor: color,
                                extendedProps: {
                                    isRotative: response.is_inverted,
                                    isOptional: response.is_optional,
                                    mandatoryShift: response.mandatory_shift,
                                    shift: shift,
                                    displayedShift: displayedShift
                                }
                            });
                        });

                        calendar.render();
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
