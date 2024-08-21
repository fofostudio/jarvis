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
                    .calendar th, .calendar td {
                        text-align: center;
                        padding: 8px;
                        border: 1px solid #dee2e6;
                    }
                    .calendar th {
                        background-color: #f8f9fa;
                    }
                    .calendar .complete { background-color: #c3e6cb; }
                    .calendar .partial { background-color: #fff3cd; }
                    .calendar .none { background-color: #f5c6cb; }
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
                    .shift-morning { background-color: #ffc107; }
                    .shift-afternoon { background-color: #17a2b8; }
                    .shift-night { background-color: #6610f2; }
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
                                        <th>Mon</th>
                                        <th>Tue</th>
                                        <th>Wed</th>
                                        <th>Thu</th>
                                        <th>Fri</th>
                                        <th>Sat</th>
                                        <th>Sun</th>
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
                                                </tr><tr>
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
                                                    <span class="shift-indicator shift-{{ $shift }} {{ in_array($shift, $dayData['shifts']) ? '' : 'd-none' }}"></span>
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

                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-body">
                        <form action="{{ route('points.index') }}" method="GET" id="filterForm">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="user_id">{{ __('admin.operator') }}</label>
                                    <select name="user_id" id="user_id" class="form-select">
                                        <option value="">{{ __('admin.all_operators') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="shift">{{ __('admin.shift') }}</label>
                                    <select name="shift" id="shift" class="form-select">
                                        <option value="">{{ __('admin.all_shifts') }}</option>
                                        @foreach($shifts as $shiftOption)
                                            <option value="{{ $shiftOption }}" {{ $shift == $shiftOption ? 'selected' : '' }}>
                                                {{ __('admin.' . $shiftOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="group_id">{{ __('admin.group') }}</label>
                                    <select name="group_id" id="group_id" class="form-select">
                                        <option value="">{{ __('admin.all_groups') }}</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}" {{ $groupId == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="date">{{ __('admin.date') }}</label>
                                    <input type="date" name="date" id="date" class="form-control" value="{{ $date }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-dark">{{ __('admin.filter') }}</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-custom border-0">
                    <div class="card-body p-lg-4">
                        <h5 class="mb-3">Records for {{ $date }}</h5>
                        <div class="table-responsive p-0">
                            <table id="recordsTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="active">{{ trans('admin.user') }}</th>
                                        <th class="active">{{ trans('admin.group') }}</th>
                                        <th class="active">{{ trans('admin.shift') }}</th>
                                        <th class="active">{{ trans('admin.date') }}</th>
                                        <th class="active">{{ trans('admin.points') }}</th>
                                        <th class="active">{{ trans('admin.goal') }}</th>
                                        <th class="active">{{ trans('admin.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($points as $point)
                                        <tr>
                                            <td>{{ $point->user->name }}</td>
                                            <td>{{ $point->group->name }}</td>
                                            <td>{{ trans('admin.' . $point->shift) }}</td>
                                            <td>{{ Helper::formatDate($point->date) }}</td>
                                            <td>{{ $point->points }}</td>
                                            <td>{{ $point->goal }}</td>
                                            <td>
                                                <form action="{{ route('points.destroy', $point) }}" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill btn-sm actionDelete">
                                                        <i class="bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">{{ trans('admin.no_points_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function filterRecords(date) {
        document.getElementById('date').value = date;
        document.getElementById('filterForm').submit();
    }
    </script>
@endsection
