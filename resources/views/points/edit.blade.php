<!-- resources/views/points/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.edit_points') }}</h2>
    <form action="{{ route('points.update', $point) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="date" class="form-label">{{ __('admin.date') }}</label>
            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $point->date->format('Y-m-d')) }}" required>
            @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="group_id" class="form-label">{{ __('admin.group') }}</label>
            <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id" required>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id', $point->group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
            @error('group_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="shift" class="form-label">{{ __('admin.shift') }}</label>
            <select class="form-select @error('shift') is-invalid @enderror" id="shift" name="shift" required>
                <option value="morning" {{ old('shift', $point->shift) == 'morning' ? 'selected' : '' }}>{{ __('admin.morning') }}</option>
                <option value="afternoon" {{ old('shift', $point->shift) == 'afternoon' ? 'selected' : '' }}>{{ __('admin.afternoon') }}</option>
                <option value="night" {{ old('shift', $point->shift) == 'night' ? 'selected' : '' }}>{{ __('admin.night') }}</option>
            </select>
            @error('shift')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="points" class="form-label">{{ __('admin.points') }}</label>
            <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points', $point->points) }}" required>
            @error('points')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="goal" class="form-label">{{ __('admin.goal') }}</label>
            <input type="number" class="form-control @error('goal') is-invalid @enderror" id="goal" name="goal" value="{{ old('goal', $point->goal) }}" required>
            @error('goal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('admin.update') }}</button>
    </form>
</div>
@endsection
