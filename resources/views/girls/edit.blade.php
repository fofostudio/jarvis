<!-- resources/views/girls/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.edit_girl') }}</h2>
    <form action="{{ route('girls.update', $girl) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('admin.name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $girl->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="internal_id" class="form-label">{{ __('admin.internal_id') }}</label>
            <input type="text" class="form-control @error('internal_id') is-invalid @enderror" id="internal_id" name="internal_id" value="{{ old('internal_id', $girl->internal_id) }}" required>
            @error('internal_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">{{ __('admin.username') }}</label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $girl->username) }}" required>
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('admin.password') }} ({{ __('admin.leave_blank_if_unchanged') }})</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="platform_id" class="form-label">{{ __('admin.platform') }}</label>
            <select class="form-select @error('platform_id') is-invalid @enderror" id="platform_id" name="platform_id" required>
                @foreach($platforms as $platform)
                    <option value="{{ $platform->id }}" {{ old('platform_id', $girl->platform_id) == $platform->id ? 'selected' : '' }}>{{ $platform->name }}</option>
                @endforeach
            </select>
            @error('platform_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="group_id" class="form-label">{{ __('admin.group') }}</label>
            <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id" required>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id', $girl->group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
            @error('group_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('admin.update') }}</button>
    </form>
</div>
@endsection
