<!-- resources/views/platforms/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.edit_platform') }}</h2>
    <form action="{{ route('platforms.update', $platform) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('admin.name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $platform->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="url" class="form-label">{{ __('admin.url') }}</label>
            <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $platform->url) }}" required>
            @error('url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="access_mode" class="form-label">{{ __('admin.access_mode') }}</label>
            <select class="form-select @error('access_mode') is-invalid @enderror" id="access_mode" name="access_mode" required>
                <option value="multi_panel" {{ old('access_mode', $platform->access_mode) == 'multi_panel' ? 'selected' : '' }}>{{ __('admin.multi_panel') }}</option>
                <option value="simple" {{ old('access_mode', $platform->access_mode) == 'simple' ? 'selected' : '' }}>{{ __('admin.simple') }}</option>
            </select>
            @error('access_mode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="color" class="form-label">{{ __('admin.color') }}</label>
            <input type="color" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $platform->color) }}" required>
            @error('color')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">{{ __('admin.logo') }}</label>
            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
            @error('logo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if($platform->logo)
                <img src="{{ asset('storage/' . $platform->logo) }}" alt="{{ $platform->name }} logo" class="mt-2" style="max-width: 200px;">
            @endif
        </div>

        <button type="submit" class="btn btn-primary">{{ __('admin.update') }}</button>
    </form>
</div>
@endsection
