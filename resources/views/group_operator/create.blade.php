<!-- resources/views/group_operator/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.create_assignment') }}</h2>

    <form action="{{ route('group_operator.create') }}" method="GET" class="mb-4">
        <div class="form-group">
            <label for="shift">{{ __('admin.shift') }}</label>
            <select name="shift" id="shift" class="form-control" onchange="this.form.submit()">
                <option value="">{{ __('admin.select_shift') }}</option>
                @foreach($shifts as $shift)
                    <option value="{{ $shift }}" {{ $selectedShift == $shift ? 'selected' : '' }}>
                        {{ __('admin.shift_' . $shift) }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if($selectedShift)
        <form action="{{ route('group_operator.store') }}" method="POST">
            @csrf
            <input type="hidden" name="shift" value="{{ $selectedShift }}">

            <div class="form-group">
                <label for="group_id">{{ __('admin.group') }}</label>
                <select name="group_id" id="group_id" class="form-control" required>
                    <option value="">{{ __('admin.select_group') }}</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="user_id">{{ __('admin.operator') }}</label>
                <select name="user_id" id="user_id" class="form-control" required>
                    <option value="">{{ __('admin.select_operator') }}</option>
                    @foreach($operators as $operator)
                        <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-sm btn-dark mt-1 mt-lg-0">{{ __('admin.create_assignment') }}</button>
        </form>
    @elseif(request('shift'))
        <div class="alert alert-info">
            {{ __('admin.no_available_assignments') }}
        </div>
    @endif
</div>
@endsection
