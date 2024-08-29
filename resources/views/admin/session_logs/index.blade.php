@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">{{ __('admin.session_logs') }}</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="card-title">Top 5 Best Attendance</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($statistics['top_attendance'] as $stat)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $stat['name'] }}
                                <span class="badge bg-primary rounded-pill">{{ $stat['count'] }} days</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="card-title">Top 5 Most Absences</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($statistics['top_absence'] as $stat)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $stat['name'] }}
                                <span class="badge bg-danger rounded-pill">{{ $stat['count'] }} days</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="card-title">Top 5 Most Late Arrivals</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($statistics['top_late'] as $stat)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $stat['name'] }}
                                <span class="badge bg-warning rounded-pill">{{ $stat['count'] }} days</span>
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
                <h3 class="card-title">Attendance - Week {{ $currentWeek->format('W') }}</h3>
                <div>
                    <a href="{{ route('admin.session_logs.index', ['week' => $prevWeek->format('Y-W')]) }}" class="btn btn-secondary">
                                           {{ __('admin.previous_week') }}
                    </a>
                    <a href="{{ route('admin.session_logs.index', ['week' => $nextWeek->format('Y-W')]) }}" class="btn btn-secondary">
                        {{ __('admin.next_week') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @foreach($shifts as $shift)
                <h4 class="mb-3">{{ ucfirst($shift) }} Shift</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-4">
                        <thead>
                            <tr>
                                <th>{{ __('admin.operator') }}</th>
                                @foreach($currentWeekDates as $date)
                                    <th class="text-danger">
                                        {{ $date->format('D, M j') }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceData[$shift] as $operator)
                                <tr>
                                    <td>
                                        @php
                                            $nameParts = explode(' ', $operator['name']);
                                            $displayName =
                                                isset($nameParts[0]) && isset($nameParts[2])
                                                    ? $nameParts[0] . ' ' . $nameParts[2]
                                                    : $operator['name'];
                                        @endphp
                                        {{ $displayName }}
                                    </td>
                                    @foreach($currentWeekDates as $date)
                                        @php
                                            $formattedDate = $date->format('Y-m-d');
                                            $status = $operator['attendance'][$formattedDate] ?? 'absent';
                                        @endphp
                                        <td class="
                                            @if($status === 'on_time') bg-success
                                            @elseif($status === 'late') bg-warning
                                            @else bg-danger
                                            @endif
                                        ">
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
@endsection
