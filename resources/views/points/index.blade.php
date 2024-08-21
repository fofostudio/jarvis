@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.points') }} ({{ $points->count() }})</span>
        <a href="{{ route('points.create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
            <i class="bi-plus-lg"></i> {{ __('admin.add_points') }}
        </a>
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
                    $startDate = new DateTime(array_key_first($calendarData));
                    $endDate = new DateTime(array_key_last($calendarData));
                    $interval = new DateInterval('P1M');
                    $months = new DatePeriod($startDate, $interval, $endDate);
                    $today = new DateTime();
                    $selectedDate = new DateTime($date);
                @endphp

                @foreach ($months as $month)
                    <div class="card shadow-custom border-0 mb-4">
                        <div class="card-body p-lg-4">
                            <h5 class="mb-3 text-center">{{ $month->format('F Y') }}</h5>
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
                                        $start = new DateTime($month->format('Y-m-01'));
                                        $end = new DateTime($month->format('Y-m-t'));
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
                    $dayData = $calendarData[$day->format('Y-m-d')] ?? ['status' => 'none', 'shifts' => []];
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
        @endforeach
        <div class="card shadow-custom border-0">
            <div class="card-body p-lg-4">
                <h5 class="mb-3">Registros del: {{ $date }}</h5>
                <div class="table-responsive p-0 records-section">
                    @include('points.records', ['points' => $points])
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <script>
        function filterRecords(date) {
            $.ajax({
                url: "{{ route('points.index') }}",
                data: {
                    date: date
                },
                success: function(response) {
                    // Actualizar la sección de registros en la vista con la respuesta del servidor
                    $('.records-section').html(response);
                }
            });
        }
    </script>
@endsection
