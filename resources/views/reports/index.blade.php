@extends('layouts.app')

@section('content')

    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.reports') }}</span>
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

                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-body p-lg-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="card-title mb-0">{{ trans('admin.reports') }}</h6>
                            <form action="{{ route('reports.index') }}" method="GET" class="d-flex align-items-center">
                                <select name="date_filter" id="date_filter" class="form-select me-2" onchange="this.form.submit()">
                                    <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>{{ trans('admin.this_month') }}</option>
                                    <option value="last_month" {{ request('date_filter') == 'last_month' ? 'selected' : '' }}>{{ trans('admin.last_month') }}</option>
                                    <option value="this_year" {{ request('date_filter') == 'this_year' ? 'selected' : '' }}>{{ trans('admin.this_year') }}</option>
                                    <option value="last_year" {{ request('date_filter') == 'last_year' ? 'selected' : '' }}>{{ trans('admin.last_year') }}</option>
                                    <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>{{ trans('admin.today') }}</option>
                                </select>
                                <button type="submit" class="btn btn-primary">{{ trans('admin.filter') }}</button>
                            </form>
                        </div>

                        <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="operator-tab" data-bs-toggle="tab" data-bs-target="#operator" type="button" role="tab" aria-controls="operator" aria-selected="true">{{ trans('admin.operator_points') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="group-tab" data-bs-toggle="tab" data-bs-target="#group" type="button" role="tab" aria-controls="group" aria-selected="false">{{ trans('admin.group_points') }}</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-4" id="reportTabsContent">
                            <div class="tab-pane fade show active" id="operator" role="tabpanel" aria-labelledby="operator-tab">
                                <div class="table-responsive p-0">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="active">{{ trans('admin.user') }}</th>
                                                <th class="active">{{ trans('admin.total_points') }}</th>
                                                <th class="active">{{ trans('admin.average_points') }}</th>
                                                <th class="active">{{ trans('admin.achieved_goals') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($userPoints as $userPoint)
                                                <tr>
                                                    <td>{{ $userPoint->name }}</td>
                                                    <td>{{ $userPoint->total_points }}</td>
                                                    <td>{{ number_format($userPoint->average_points, 2) }}</td>
                                                    <td>{{ $userPoint->achieved_goals }} / {{ $userPoint->total_goals }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">{{ trans('admin.no_user_points_found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="group" role="tabpanel" aria-labelledby="group-tab">
                                <div class="table-responsive p-0">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="active">{{ trans('admin.group') }}</th>
                                                <th class="active">{{ trans('admin.total_points') }}</th>
                                                <th class="active">{{ trans('admin.average_points') }}</th>
                                                <th class="active">{{ trans('admin.achieved_goals') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($groupPoints as $groupPoint)
                                                <tr>
                                                    <td>{{ $groupPoint->name }}</td>
                                                    <td>{{ $groupPoint->total_points }}</td>
                                                    <td>{{ number_format($groupPoint->average_points, 2) }}</td>
                                                    <td>{{ $groupPoint->achieved_goals }} / {{ $groupPoint->total_goals }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">{{ trans('admin.no_group_points_found') }}</td>
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
        </div>
    </div>
@endsection
