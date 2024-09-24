
<!-- resources/views/foodAdmin/categories/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create Category</h2>
        <form action="{{ route('foodAdmin.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Category</button>
        </form>
    </div>
@endsection
