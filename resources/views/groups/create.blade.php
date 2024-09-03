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

        <div class="mb-3">
            <label for="group_category_id" class="form-label">{{ __('admin.category') }}</label>
            <select class="form-select @error('group_category_id') is-invalid @enderror" id="group_category_id" name="group_category_id" required>
                <option value="">{{ __('admin.select_category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('group_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('group_category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('admin.create') }}</button>
    </form>
</div>
@endsection
