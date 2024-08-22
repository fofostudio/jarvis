<!-- resources/views/users/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.create_user') }}</h2>
    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('admin.name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('admin.email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('admin.password') }}</label>
            <input value="TeamSic2024" class="form-control @error('password') is-invalid @enderror" id="password" name="password" readonly>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <input value="TeamSic2024" type="password" class="form-control" id="password_confirmation" name="password_confirmation" hidden>
        </div>


        <div class="mb-3">
            <label for="identification" class="form-label">{{ __('admin.identification') }}</label>
            <input type="text" class="form-control @error('identification') is-invalid @enderror" id="identification" name="identification" value="{{ old('identification') }}" required>
            @error('identification')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
               <div class="mb-3">
            <label for="birth_date" class="form-label">{{ __('admin.birth_date') }}</label>
            <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
            @error('birth_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('admin.phone') }}</label>
            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">{{ __('admin.address') }}</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="neighborhood" class="form-label">{{ __('admin.neighborhood') }}</label>
            <input type="text" class="form-control @error('neighborhood') is-invalid @enderror" id="neighborhood" name="neighborhood" value="{{ old('neighborhood') }}" required>
            @error('neighborhood')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">{{ __('admin.create') }}</button>
    </form>
</div>
@endsection
