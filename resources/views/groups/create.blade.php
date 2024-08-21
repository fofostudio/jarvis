<!-- resources/views/groups/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.create_group') }}</h2>
    <form action="{{ route('groups.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('admin.name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">{{ __('admin.create') }}</button>
    </form>
</div>
@endsection
