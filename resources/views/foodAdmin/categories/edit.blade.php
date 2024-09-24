

<!-- resources/views/foodAdmin/categories/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Category</h2>
        <form action="{{ route('foodAdmin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description">{{ $category->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>
    </div>
@endsection
