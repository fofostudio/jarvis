@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.points') }} ({{ $points->count() }})</span>
        @if (auth()->user()->role == 'admin')
        @else
            <a href="{{ route('points.create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
                <i class="bi-plus-lg"></i> {{ __('admin.add_points') }}
            </a>
        @endif
    </h5>

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                @if (session('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                <style>
                    .calendar {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    .calendar th,
                    .calendar td {
                        text-align: center;
                        padding: 8px;
                        border: 1px solid #dee2e6;
                    }

                    .calendar th {
                        background-color: #f8f9fa;
                    }

                    .calendar .complete {
                        background-color: #c3e6cb;
                    }

                    .calendar .partial {
                        background-color: #fff3cd;
                    }

                    .calendar .none {
                        background-color: #f5c6cb;
                    }

                    .calendar .today {
                        font-weight: bold;
                        outline: 2px solid #007bff;
                    }

                    .calendar td {
                        cursor: pointer;
                    }

                    .calendar td:hover {
                        background-color: #e9ecef;
                    }

                    .shift-indicator {
                        display: inline-block;
                        width: 10px;
                        height: 10px;
                        margin: 1px;
                        border-radius: 50%;
                    }

                    .shift-morning {
                        background-color: #ffc107;
                    }

                    .shift-afternoon {
                        background-color: #17a2b8;
                    }

                    .shift-night {
                        background-color: #6610f2;
                    }
                </style>

                @php
                    $currentMonth = new DateTime();
                    $today = new DateTime();
                    $selectedDate = new DateTime($date);
                @endphp

                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-body p-lg-4">
                        <h5 class="mb-3 text-center">{{ $currentMonth->format('F Y') }}</h5>
                        <table class="calendar">
                            <thead>
                                <tr>
                                    <th>Lun</th>
                                    <th>Mar</th>
                                    <th>Mier</th>
                                    <th>Jue</th>
                                    <th>Vier</th>
                                    <th>Sáb</th>
                                    <th>Dom</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $start = new DateTime($currentMonth->format('Y-m-01'));
                                    $end = new DateTime($currentMonth->format('Y-m-t'));
                                    $interval = new DateInterval('P1D');
                                    $days = new DatePeriod($start, $interval, $end);
                                @endphp
                                <tr>
                                    @for ($i = 1; $i < $start->format('N'); $i++)
                                        <td></td>
                                    @endfor
                                    @foreach ($days as $day)
                                        @if ($day->format('N') == 1 && $day->format('j') > 1)
                                </tr>
                                <tr>
                                    @endif
                                    @php
                                        $dayData = $calendarData[$day->format('Y-m-d')] ?? [
                                            'status' => 'none',
                                            'shifts' => [],
                                        ];
                                    @endphp
                                    <td class="{{ $dayData['status'] }}
                                   {{ $day->format('Y-m-d') == $today->format('Y-m-d') ? 'today' : '' }}
                                   {{ $day->format('Y-m-d') == $selectedDate->format('Y-m-d') ? 'selected' : '' }}"
                                        onclick="filterRecords('{{ $day->format('Y-m-d') }}')">
                                        {{ $day->format('d') }}<br>
                                        @foreach (['morning', 'afternoon', 'night'] as $shift)
                                            <span
                                                class="shift-indicator shift-{{ $shift }} {{ in_array($shift, $dayData['shifts']) ? '' : 'd-none' }}"></span>
                                        @endforeach
                                    </td>
                                    @endforeach
                                    @for ($i = $end->format('N'); $i < 7; $i++)
                                        <td></td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card shadow-custom border-0">
                    <div class="card-body p-lg-4">
                        <h5 class="mb-3">Registros</h5>
                        <div class="table-responsive p-0 records-section">
                            @include('points.records', ['points' => $points])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar puntos -->
    <div class="modal fade" id="editPointModal" tabindex="-1" aria-labelledby="editPointModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPointModalLabel">{{ __('admin.edit_points') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- El formulario se cargará aquí dinámicamente -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function filterRecords(date) {
            $.ajax({
                url: '{{ route('points.index') }}',
                type: 'GET',
                data: {
                    date: date
                },
                success: function(response) {
                    $('.records-section').html(response);
                },
                error: function(xhr) {
                    console.error('Error loading records:', xhr.responseText);
                }
            });
        }

        function openEditModal(pointId) {
            $.ajax({
                url: `/points/${pointId}/edit`,
                type: 'GET',
                success: function(response) {
                    $('#editPointModal .modal-body').html(response);
                    $('#editPointModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Error loading edit form:', xhr.responseText);
                }
            });
        }

        $(document).on('submit', '#editPointForm', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#editPointModal').modal('hide');
                    // Actualizar la tabla de registros
                    $('.records-section').html(response.records);
                    // Mostrar mensaje de éxito
                    alert('Points updated successfully');
                },
                error: function(xhr) {
                    console.error('Error updating points:', xhr.responseText);
                    // Mostrar errores de validación si los hay
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    }
                }
            });
        });
    </script>
@endpush
