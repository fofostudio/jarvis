@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.session_logs') }}</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.session_logs.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">{{ __('admin.start_date') }}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">{{ __('admin.end_date') }}</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="user_id">{{ __('admin.user') }}</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">{{ __('admin.all_users') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-sm btn-dark mt-1 mt-lg-0">{{ __('admin.filter') }}</button>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.date') }}</th>
                            <th>{{ __('admin.user') }}</th>
                            <th>{{ __('admin.first_login') }}</th>
                            <th>{{ __('admin.last_logout') }}</th>
                            <th>{{ __('admin.login_count') }}</th>
                            <th>{{ __('admin.ip_address') }}</th>
                            <th>{{ __('admin.user_agent') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessionLogs as $log)
                            <tr>
                                <td>{{ $log->date->format('Y-m-d') }}</td>
                                <td>{{ $log->user->name }}</td>
                                <td>{{ $log->first_login ? $log->first_login->format('Y-m-d H:i:s') : '-' }}</td>
                                <td>{{ $log->last_logout ? $log->last_logout->format('Y-m-d H:i:s') : '-' }}</td>
                                <td>{{ $log->login_count }}</td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ Str::limit($log->user_agent, 30) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $sessionLogs->links() }}
        </div>
    </div>
</div>
@endsection
