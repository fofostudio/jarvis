<!-- resources/views/users/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.edit_user') }}</h2>
    <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('admin.name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('admin.email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
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
            <label for="avatar" class="form-label">{{ __('admin.avatar') }}</label>
            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar">
            @error('avatar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }} avatar" class="mt-2" style="max-width: 200px;">
            @endif
        </div>

        <div class="mb-3">
            <label for="identification" class="form-label">{{ __('admin.identification') }}</label>
            <input type="text" class="form-control @error('identification') is-invalid @enderror" id="identification" name="identification" value="{{ old('identification', $user->identification) }}" required>
            @error('identification')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="birth_date" class="form-label">{{ __('admin.birth_date') }}</label>
            <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date->format('Y-m-d')) }}" required>
            @error('birth_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('admin.phone') }}</label>
            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">{{ __('admin.address') }}</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}" required>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="neighborhood" class="form-label">{{ __('admin.neighborhood') }}</label>
            <input type="text" class="form-control @error('neighborhood') is-invalid @enderror" id="neighborhood" name="neighborhood" value="{{ old('neighborhood', $user->neighborhood) }}" required>
            @error('neighborhood')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('admin.update') }}</button>
    </form>
</div>
@endsection
