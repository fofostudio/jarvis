@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.edit_assignment') }}</h2>

    <form action="{{ route('group_operator.update', $assignment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="shift">{{ __('admin.shift') }}</label>
            <select name="shift" id="shift" class="form-control" required>
                @foreach(['morning', 'afternoon', 'night', 'complete'] as $shift)
                    <option value="{{ $shift }}" {{ old('shift', $assignment->shift) == $shift ? 'selected' : '' }}>
                        {{ __('admin.shift_' . $shift) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="group_id">{{ __('admin.group') }}</label>
            <select name="group_id" id="group_id" class="form-control" required>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id', $assignment->group_id) == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="user_id">{{ __('admin.operator') }}</label>
            <select name="user_id" id="user_id" class="form-control" required>
                @foreach($operators as $operator)
                    <option value="{{ $operator->id }}" {{ old('user_id', $assignment->user_id) == $operator->id ? 'selected' : '' }}>
                        {{ $operator->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('admin.update_assignment') }}</button>
    </form>
</div>
@endsection
